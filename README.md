<p align="center"><h1>Rancho do Radioamador Gaúcho</h1></p>


## Sobre o evento

O Rancho do Radioamador Gaúcho é um dos mais antigos encontros de radioamadores do Brasil ainda em atividade, comemorando neste ano (2025) sua 70º edição. O "rancho", como carinhosamente é chamado pelos radioamadores proporciona um espaço de:

- Aprendizado, com palestras e workshops práticos
- Confraternização familiar. Onde todas famílias se conhecem, se reencontram e compartilham de momentos ímpares juntos
- Troca de equipamentos, com a tradicional feirinha de troca-troca
- Convívio social, com seu tradicional baile de sábado a noite e eventos para as cristais
- Lazer, o rancho geralmente é organizado em hotéis ou espaços que também proporcionam a oportunidade de acampamento e contato com a natureza

## A aplicação de inscrições

A aplicação de inscrições foi desenvolvida em sua primeira versão para a 69º edição, realizada em Pelotas/RS pelo [Rádio Clube de Pelotas](https://py3rcp.org/). Em razão da 70º edição em Santa Maria ([USRA](https://py3ur.blogspot.com/)), seu desenvolvedor [Nataniel Kegles (PY3NT)](https://www.kegles.com.br/contrate), resolveu doar a aplicação para as próximas edições e abri-la em formato "copyleft", onde cada um pode copiar e modificar conforme precisar sem pagar royalties para o desenvolvedor original, pedindo apenas que mantenha os créditos originais no rodapé da aplicação por mera lembrança.

Essa aplicação é desenvolvida em linguagem [PHP](https://php.net/) usando a framework [Laravel](https://laravel.com/) e base de dados [MariaDB](https://mariadb.org/).

## Instalação da aplicação

- Você precisa subir os arquivos da aplicação em seu host hospedado (VPS) com PHP/MariaDB ou MySQL, depois configure os parâmetros do .env:

RANCHO_PIX_KEY="as23f3"                             #chave pix (aleatória) para onde irá o dinheiro das inscrições
RANCHO_PIX_NAME="70 RANCHO DO RADIOAMADOR GAUCHO"   #nome da edição do rancho (sem caracteres especiais)
RANCHO_MERCHANT_CITY="SANTA MARIA"                  #cidade onde será realizado o rancho (sem caracteres especiais)
~
APP_URL=http://rancho.com.br                        #host da aplicação
~
DB_CONNECTION=mysql
DB_HOST=127.0.0.1                                   # host do db
DB_PORT=3306                                        # ou a sua porta real
DB_DATABASE=rancho                                  # seu banco
DB_USERNAME=root                                    # seu usuário
DB_PASSWORD=                                        # sua senha, se houver
DB_SOCKET=                                          # deixe VAZIO


- Executar os seguintes comandos via terminal:

$chmod -R 777 storage/
$php artisan migrate
$php artisan db:seed
$php artisan config:clear

## Licença

Este software é distribuido pela licença [MIT license](https://opensource.org/licenses/MIT).
