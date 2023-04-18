#!/usr/bin/env bash

wget $(wget -q -O - 'https://api.github.com/repos/nginxinc/nginx-prometheus-exporter/releases/latest' | jq -r '.assets[] | select(.name | test("^nginx-prometheus-exporter.*linux_amd64.tar.gz$")).browser_download_url')
tar xf nginx-prometheus-exporter*linux_amd64.tar.gz nginx-prometheus-exporter
chmod +x nginx-prometheus-exporter
rm nginx-prometheus-exporter*linux_amd64.tar.gz

wget -O php-fpm_exporter $(wget -q -O - 'https://api.github.com/repos/hipages/php-fpm_exporter/releases/latest' | jq -r '.assets[] | select(.name | test("^php-fpm_exporter_.*_linux_amd64$")).browser_download_url')
chmod +x php-fpm_exporter

wget $(wget -q -O - 'https://api.github.com/repos/grafana/agent/releases/latest' | jq -r '.assets[] | select(.name=="grafana-agent-linux-amd64.zip").browser_download_url')
unzip grafana-agent-linux-amd64.zip
mv 
