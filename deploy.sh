#!/bin/bash

# 設定値を変更してください
EC2_IP="YOUR_EC2_PUBLIC_IP"
GITHUB_REPO="nakashima63/todo-app"
KEY_PATH="/Users/nakashimasoya/Desktop/test-ec2-key.pem"

echo "=== Laravel Todo App デプロイスクリプト ==="
echo "EC2 IP: $EC2_IP"
echo "GitHub Repo: $GITHUB_REPO"

# EC2でセットアップコマンドを実行
echo "1. GitHubからアプリケーションをクローン中..."
ssh -i $KEY_PATH -o StrictHostKeyChecking=no ubuntu@$EC2_IP << EOF
  # 既存のファイルを削除
  sudo rm -rf /var/www/html/*
  
  # GitHubからクローン
  cd /tmp
  rm -rf todo-app
  git clone https://github.com/$GITHUB_REPO.git todo-app
  
  # アプリケーションディレクトリに移動
  sudo cp -r /tmp/todo-app/* /var/www/html/
  cd /var/www/html
  
  # 依存関係インストール
  sudo -u www-data composer install --no-dev --optimize-autoloader
  sudo -u www-data npm install
  sudo -u www-data npm run build
  
  # 環境設定
  sudo -u www-data cp .env.example .env
  sudo -u www-data php artisan key:generate
  
  # データベースセットアップ
  sudo -u www-data touch database/database.sqlite
  sudo -u www-data php artisan migrate --force
  
  # 権限設定
  sudo chown -R www-data:www-data /var/www/html
  sudo chmod -R 755 /var/www/html
  sudo chmod -R 775 /var/www/html/storage
  sudo chmod -R 775 /var/www/html/bootstrap/cache
  
  echo "セットアップ完了!"
EOF

echo "3. Nginx設定を更新中..."
ssh -i $KEY_PATH -o StrictHostKeyChecking=no ubuntu@$EC2_IP << 'EOF'
  # Nginx設定ファイル作成
  sudo tee /etc/nginx/sites-available/todoapp > /dev/null << 'NGINX_CONFIG'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINX_CONFIG

  # サイト有効化
  sudo ln -sf /etc/nginx/sites-available/todoapp /etc/nginx/sites-enabled/
  sudo rm -f /etc/nginx/sites-enabled/default
  
  # Nginx再起動
  sudo nginx -t && sudo systemctl reload nginx
  
  echo "Nginx設定完了!"
EOF

echo "=== デプロイ完了 ==="
echo "アプリケーションURL: http://$EC2_IP"