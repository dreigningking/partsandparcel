# AWS Production Cloud Architecture Guide

## Parts & Parcel Deployment & Cloud Infrastructure Blueprint

This guide is your hands-on manual for deploying **Parts & Parcel** to Amazon Web Services (AWS). It covers the strategic choice between **Amazon Lightsail** and **Amazon EC2**, followed by step-by-step implementation runbooks for both paths.

---

## 1. Amazon Lightsail vs. Amazon EC2: Strategic Comparison

Both Lightsail and EC2 run virtual servers on AWS hardware, but they serve different stages of a company's lifecycle.

| Feature | Amazon Lightsail | Amazon EC2 |
| :--- | :--- | :--- |
| **Pricing Model** | **Predictable, flat monthly bundle** (e.g., $5, $10, $20/mo). | **Pay-as-you-go** granular billing (CPU, RAM, EBS disk, IOPS, EIP). |
| **Bandwidth (Data Transfer)** | **Generous allowance included** (1 TB – 3 TB/month free in the bundle). | Billed per GB transferred out (~$0.09/GB after first 100 GB). |
| **Networking Setup** | **Zero-hassle**: Static IP and firewall rules configured in 2 clicks. | **Full VPC**: Subnets, Route Tables, Internet Gateways, NAT Gateways. |
| **Scaling** | **Vertical scaling**: Resize instance to larger bundle in 2 minutes. | **Horizontal scaling**: Auto Scaling Groups, Elastic Load Balancers across Availability Zones. |
| **Managed Databases** | Simple managed MySQL/PostgreSQL available ($15/mo). | AWS RDS (Multi-AZ, Read Replicas, Aurora Serverless). |
| **Best For** | **MVP, Launch, Initial 10,000 Users, Predictable Budget**. | **High-Traffic Scale, Enterprise Compliance, Multi-Region**. |

---

### The Executive Recommendation for Parts & Parcel

```
   [ STAGE 1: LAUNCH & INITIAL TRACTION ] ──────────▶ [ STAGE 2: HIGH-GROWTH SCALE ]
   Amazon Lightsail ($5 - $10 / month)                Amazon EC2 + RDS + ALB
   - Predictable cost (no surprise bills)             - Multi-AZ automatic failover
   - 1 TB to 3 TB free outbound bandwidth             - Horizontal auto-scaling (2 to 10 nodes)
   - Runs our exact Docker container                  - Enterprise VPC security isolation
```

> [!TIP]
> **Why You Can Start on Lightsail Without Fear:**
> Because we built **Docker** in Milestone 2 and **GitHub Actions** in Milestone 4, your application is completely decoupled from the underlying server. Running on Lightsail takes 15 minutes, and moving to EC2 later requires **zero code rewrites**—you just point your deployment script to a new IP!

---

## 2. Option A: Amazon Lightsail Setup Runbook (Recommended for Launch)

### Step 1: Create an Instance
1. Sign in to the AWS Console and search for **Lightsail**.
2. Click **Create instance**.
3. **Select platform**: `Linux/Unix`.
4. **Select blueprint**: `OS Only` ──▶ `Ubuntu 24.04 LTS`.
5. **Choose your instance plan**:
   * **$5/month**: 1 GB RAM, 1 vCPU, 40 GB SSD, 1 TB transfer. *(Good for testing)*
   * **$10/month** (Recommended): 2 GB RAM, 2 vCPUs, 60 GB SSD, 3 TB transfer. *(Optimal for Livewire + Redis + Queue Workers)*
6. **Identify your instance**: `partsandparcel-prod`.
7. Click **Create instance**.

### Step 2: Attach a Static IP
Lightsail instances receive a new IP on restart unless you attach a permanent static IP (free):
1. In the Lightsail dashboard, navigate to the **Networking** tab.
2. Click **Create Static IP**.
3. Select your instance (`partsandparcel-prod`) and click **Create**.
4. Note your public IP address (e.g., `54.210.xx.xx`).

### Step 3: Configure Firewall Rules
Under the **Networking** tab of your instance, ensure these ports are open:
* **Port 22 (SSH)**: For remote administration and GitHub Actions deploy.
* **Port 80 (HTTP)**: For incoming web traffic.
* **Port 443 (HTTPS)**: For encrypted SSL traffic.

### Step 4: Prepare the Server with Docker (One-Time Setup)
Click the **Connect using SSH** browser button or connect from your terminal:
```bash
# 1. Update Ubuntu packages
sudo apt update && sudo apt upgrade -y

# 2. Install Docker using the official automated script
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# 3. Allow your user to run Docker without sudo
sudo usermod -aG docker $USER
newgrp docker

# 4. Create the project directory
sudo mkdir -p /var/www/partsandparcel
sudo chown -R $USER:$USER /var/www/partsandparcel
```

---

## 3. Option B: Amazon EC2 + RDS Setup Runbook (For Scaled High Availability)

When you are ready to scale beyond a single server:

```
[ THE INTERNET ]
       │
       ▼ (HTTPS 443)
[ AWS Route 53 DNS ] ──▶ [ AWS CloudFront CDN ] (Caches S3 images & static Vite bundles)
                               │
                               ▼
            [ AWS Application Load Balancer (ALB) ]
                               │
            ┌──────────────────┴──────────────────┐
            ▼                                     ▼
   [ EC2 Public Subnet A ]               [ EC2 Public Subnet B ]
   (partsandparcel_app)                  (partsandparcel_app)
            │                                     │
            └──────────────────┬──────────────────┘
                               ▼
            [ AWS Private Database Subnet ]
            [ AWS RDS MySQL Multi-AZ Cluster ]
```

### Step 1: AWS S3 Bucket Setup for Uploads & Invoices
1. Go to **S3** in the AWS Console and click **Create bucket**.
2. **Bucket name**: `partsandparcel-media-prod` (must be globally unique).
3. **Region**: `us-east-1` (or your nearest region, e.g. `eu-west-1`).
4. **Block Public Access**: Keep all blocks checked (Best practice: serve images through CloudFront with Origin Access Control).
5. **CORS Configuration**: Under the **Permissions** tab, add:
   ```json
   [
     {
       "AllowedHeaders": ["*"],
       "AllowedMethods": ["GET", "PUT", "POST", "HEAD"],
       "AllowedOrigins": ["https://partsandparcel.com"],
       "ExposeHeaders": ["ETag"]
     }
   ]
   ```

### Step 2: AWS RDS MySQL Database Setup
1. In the AWS Console, search for **RDS** and click **Create database**.
2. **Engine**: MySQL 8.0.
3. **Templates**: `Free Tier` (or `Production` for Multi-AZ automatic failover).
4. **DB instance identifier**: `partsandparcel-mysql`.
5. **Master username**: `pp_master_admin`.
6. **Master password**: Use a strong generated 24-character password.
7. **Connectivity**:
   * **VPC**: Default VPC.
   * **Public access**: **No** (Database should NEVER be publicly exposed to the internet).
   * **VPC Security Group**: Create a new group `rds-mysql-sg` allowing incoming Port 3306 **only** from the EC2 security group.
8. Click **Create database** and copy the **Endpoint** DNS address once created.

---

## 4. Production `.env` Checklist for AWS

Create your production `.env` file on the server (`/var/www/partsandparcel/.env`):

```ini
APP_NAME="Parts & Parcel"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://partsandparcel.com

# Database Connection (RDS or Lightsail Database)
DB_CONNECTION=mysql
DB_HOST=partsandparcel-mysql.xxxxxx.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=partsandparcel_prod
DB_USERNAME=pp_user
DB_PASSWORD=YOUR_STRONG_DB_PASSWORD

# In-Memory Cache, Sessions & Queues (Redis)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Object Storage (AWS S3)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=YOUR_IAM_ACCESS_KEY
AWS_SECRET_ACCESS_KEY=YOUR_IAM_SECRET_KEY
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=partsandparcel-media-prod
AWS_CLOUDFRONT_URL=https://d123456789.cloudfront.net

# Payment Gateways (Live Keys)
PAYSTACK_PUBLIC_KEY=your_paystack_public_key_here
PAYSTACK_SECRET_KEY=your_paystack_secret_key_here
FLUTTERWAVE_PUBLIC_KEY=your_flutterwave_public_key_here
FLUTTERWAVE_SECRET_KEY=your_flutterwave_secret_key_here

# Email (AWS SES or Mailgun/Postmark)
MAIL_MAILER=ses
AWS_SES_REGION=us-east-1
MAIL_FROM_ADDRESS="no-reply@partsandparcel.com"
MAIL_FROM_NAME="Parts & Parcel"
```

---

## 5. Security Best Practices Checklist for AWS

1. **IAM Principle of Least Privilege**: Never use your AWS Root credentials in `.env` or GitHub Actions. Create an IAM User (e.g. `partsandparcel-deployer`) with only `AmazonS3FullAccess` and `AmazonEC2ContainerRegistryPowerUser`.
2. **Never Expose Port 3306 or 6379 to 0.0.0.0/0**: MySQL and Redis ports should only accept connections from your application server's local subnet or security group.
3. **Automated RDS Snapshots**: Enable 7-day retention for automated daily backups on RDS.
4. **CloudWatch Budget Alerts**: Set an AWS Billing Alert for **$20/month** so AWS emails you instantly if any service starts accumulating unexpected costs.
