### Nginx 安装说明

server {
    listen      80;
    server_name www.process.com;
    root        /data/WorkProcess/public;
    index       index.php index.html index.htm;
    charset     utf-8;

    location / {
        try_files $uri $uri/ /index.php?_url=$uri&$args;
    }

    location ~ \.php {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;

        include fastcgi_params;
        fastcgi_split_path_info       ^(.+\.php)(/.+)$;
        fastcgi_param PATH_INFO       $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}


### Apache 安装说明

<IfModule mod_rewrite.c>
    <Directory "/data/WorkProcess/public">
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^((?s).*)$ index.php?_url=/$1 [QSA,L]
    </Directory>
</IfModule>

<VirtualHost *:80>
    DocumentRoot "/data/WorkProcess/public"
    ServerName www.process.com
    ServerAlias 
  <Directory "/data/WorkProcess/public">
      Options FollowSymLinks ExecCGI
      AllowOverride All
      Order allow,deny
      Allow from all
      Require all granted
  </Directory>
</VirtualHost>


