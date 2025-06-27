Para rodar o projeto é necessário dá permissão de execução no start.sh

No linux basta fazer chmod +x start.sh

E para rodar o projeto faça ./start.sh

Para criar um usuário basta acessar a URL -> http://localhost/register

Curls:

Para pegar o token:
    curl --location 'http://localhost/v1/auth/token' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "admin@book.com",
    "password": "12345678"
  }'

Para lista os livros:
    curl --location 'http://localhost/v1/livros?titulo=111111&titulo_do_indice=2222' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 1|19EEA0F6AWSk8V7XqwEcZPFg2rQ0pVbbud1hYt5z7ff9578d' \

Para criar um livro:
    curl --location 'http://localhost/api/v1/livros' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 1|19EEA0F6AWSk8V7XqwEcZPFg2rQ0pVbbud1hYt5z7ff9578d' \
--header 'Content-Type: application/json' \
--data '{
    "titulo": "111111",
    "indices": [
        {
            "titulo":"indice 111",
            "pagina": 2,
            "subindices": [
                {
                    "titulo": "indicice 1.1",
                    "pagina": 3,
                    "subindices": []
                }
            ]
        },
         {
            "titulo":"22222",
            "pagina": 4,
            "subindices": [
            ]
        }
    ]
  }'

Para adicionar um indice em XML
    curl --location 'http://localhost/api/v1/livros/15/importar-indices-xml' \
--header 'Accept: application/xml' \
--header 'Authorization: Bearer 1|19EEA0F6AWSk8V7XqwEcZPFg2rQ0pVbbud1hYt5z7ff9578d' \
--header 'Content-Type: application/xml' \
--data '<indice>
    <item pagina="1" titulo="Secao 1">
        <item pagina="1" titulo="Secao 1.1">
            <item pagina="1" titulo="Secao 1.1.1" />
            <item pagina="1" titulo="Secao 1.1.2" />
        </item>
         <item pagina="1" titulo="Secao 1.2" />
    </item>
    <item pagina="2" titulo="Secao 2" />
    <item pagina="3" titulo="Secao " />
</indice>'
