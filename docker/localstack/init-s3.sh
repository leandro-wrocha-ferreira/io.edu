#!/bin/bash
set -e

echo "Initializing LocalStack S3 bucket..."
awslocal s3 mb s3://ioedu-media || true

awslocal s3api put-bucket-cors --bucket ioedu-media --cors-configuration '{
  "CORSRules": [
    {
      "AllowedHeaders": ["*"],
      "AllowedMethods": ["GET", "PUT", "POST", "DELETE", "HEAD"],
      "AllowedOrigins": ["*"],
      "ExposeHeaders": ["ETag"]
    }
  ]
}'
echo "S3 bucket ioedu-media configured with CORS."
