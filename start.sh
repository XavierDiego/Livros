#!/bin/bash

set -e

function composer_install_docker() {
  echo "📦 Instalando dependências PHP via container Docker (PHP 8.2)..."
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install
}

if [ ! -f ./vendor/bin/sail ]; then
  composer_install_docker
fi

echo "🚀 Subindo containers Laravel Sail..."
./vendor/bin/sail up -d

echo "⏳ Aguardando container Laravel subir..."
while [ -z "$(docker ps -q -f name=livros_laravel.test_1)" ]; do
  sleep 2
done

echo "📦 Instalando pacotes npm..."
./vendor/bin/sail npm install

echo "🎨 Compilando frontend..."
./vendor/bin/sail npm run dev

echo "🧱 Executando migrations..."
./vendor/bin/sail artisan migrate

echo "🧵 Iniciando queue worker..."
./vendor/bin/sail artisan queue:work --tries=3 --timeout=60 &

echo "✅ Projeto iniciado com sucesso!"

