#!/bin/bash

./vendor/bin/sail up -d


while [ -z "$(docker ps -q -f name=book_laravel.test_1)" ]; do
  sleep 2
done

./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan queue:work --tries=3 --timeout=60 &

echo "✅ Projeto iniciado com sucesso!"
