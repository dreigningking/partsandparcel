document.addEventListener('alpine:init', () => {
  Alpine.data('webcamCapture', () => ({
    cameraModalOpen: false,
    stream: null,
    cameraError: '',
    captureMode: 'photo',
    isRecording: false,
    mediaRecorder: null,
    recordedChunks: [],
    recordingSeconds: 0,
    recordingTimer: null,

    async openCamera(mode = 'photo') {
      this.cameraError = '';
      this.captureMode = mode;
      this.isRecording = false;
      this.recordingSeconds = 0;
      if (this.recordingTimer) clearInterval(this.recordingTimer);

      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        try {
          const constraints = { 
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, 
            audio: mode === 'video'
          };
          this.stream = await navigator.mediaDevices.getUserMedia(constraints);
          this.cameraModalOpen = true;
          this.$nextTick(() => {
            if (this.$refs.webcamVideo) {
              this.$refs.webcamVideo.srcObject = this.stream;
              this.$refs.webcamVideo.play().catch(e => console.warn(e));
            }
          });
          return;
        } catch (err) {
          console.warn('WebRTC camera unavailable or permission denied:', err);
          if (mode === 'video') {
            try {
              this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
              });
              this.cameraModalOpen = true;
              this.$nextTick(() => {
                if (this.$refs.webcamVideo) {
                  this.$refs.webcamVideo.srcObject = this.stream;
                  this.$refs.webcamVideo.play().catch(e => console.warn(e));
                }
              });
              return;
            } catch (err2) {
              console.warn('Fallback video-only getUserMedia failed:', err2);
            }
          }
        }
      }
      const camInput = document.getElementById('cameraUploadInput');
      if (camInput) camInput.click();
    },

    async switchMode(mode) {
      if (this.isRecording) return;
      if (this.captureMode === mode) return;
      this.closeCamera();
      await this.openCamera(mode);
    },

    closeCamera() {
      if (this.isRecording) {
        this.stopRecording(false);
      }
      if (this.recordingTimer) clearInterval(this.recordingTimer);
      this.isRecording = false;
      this.recordingSeconds = 0;
      if (this.stream) {
        this.stream.getTracks().forEach(track => track.stop());
        this.stream = null;
      }
      this.cameraModalOpen = false;
    },

    takeSnapshot() {
      const video = this.$refs.webcamVideo;
      if (!video) return;
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth || 640;
      canvas.height = video.videoHeight || 480;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      canvas.toBlob((blob) => {
        if (blob) {
          const file = new File([blob], 'camera-capture-' + Date.now() + '.jpg', { type: 'image/jpeg' });
          if (this.$wire) {
            this.$wire.upload('photos', file);
          }
        }
        this.closeCamera();
      }, 'image/jpeg', 0.92);
    },

    startRecording() {
      if (!this.stream) return;
      this.recordedChunks = [];
      let options = {};
      if (typeof MediaRecorder !== 'undefined') {
        if (MediaRecorder.isTypeSupported('video/webm;codecs=vp9,opus')) {
          options = { mimeType: 'video/webm;codecs=vp9,opus' };
        } else if (MediaRecorder.isTypeSupported('video/webm')) {
          options = { mimeType: 'video/webm' };
        } else if (MediaRecorder.isTypeSupported('video/mp4')) {
          options = { mimeType: 'video/mp4' };
        }
      }
      try {
        this.mediaRecorder = new MediaRecorder(this.stream, options);
      } catch (e) {
        this.mediaRecorder = new MediaRecorder(this.stream);
      }
      this.mediaRecorder.ondataavailable = (e) => {
        if (e.data && e.data.size > 0) {
          this.recordedChunks.push(e.data);
        }
      };
      this.mediaRecorder.onstop = () => {
        if (this.recordedChunks.length > 0) {
          const mimeType = this.mediaRecorder.mimeType || 'video/webm';
          const ext = mimeType.includes('mp4') ? 'mp4' : 'webm';
          const blob = new Blob(this.recordedChunks, { type: mimeType });
          const file = new File([blob], 'camera-video-' + Date.now() + '.' + ext, { type: mimeType });
          if (this.$wire) {
            this.$wire.upload('photos', file);
          }
        }
        this.closeCamera();
      };
      this.mediaRecorder.start(1000);
      this.isRecording = true;
      this.recordingSeconds = 0;
      this.recordingTimer = setInterval(() => {
        this.recordingSeconds++;
      }, 1000);
    },

    stopRecording(save = true) {
      if (!this.mediaRecorder || this.mediaRecorder.state === 'inactive') return;
      if (this.recordingTimer) clearInterval(this.recordingTimer);
      if (!save) {
        this.mediaRecorder.onstop = null;
        this.mediaRecorder.stop();
        this.closeCamera();
      } else {
        this.mediaRecorder.stop();
      }
    },

    formatTime(sec) {
      const mins = Math.floor(sec / 60);
      const secs = sec % 60;
      return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
    }
  }));
});
