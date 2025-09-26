# Rancho do Radioamador Gaúcho

## Sobre o evento
O **Rancho do Radioamador Gaúcho** é um dos encontros de radioamadores mais antigos do Brasil ainda em atividade, comemorando em 2025 a sua **70ª edição**.  

O “rancho”, como é carinhosamente chamado, proporciona um espaço de:

- 📡 **Aprendizado**: palestras e workshops práticos  
- 👨‍👩‍👧‍👦 **Confraternização familiar**: reencontros e momentos únicos entre famílias  
- 🔧 **Troca de equipamentos**: a tradicional feirinha de troca-troca  
- 🎶 **Convívio social**: baile de sábado à noite e atividades para as “cristais”  
- 🌲 **Lazer**: geralmente organizado em hotéis ou espaços que permitem acampamento e contato com a natureza  

---

## Sobre a aplicação de inscrições
A aplicação foi criada inicialmente para a **69ª edição**, realizada em Pelotas/RS pelo [Rádio Clube de Pelotas](https://py3rcp.org/).  

Para a 70ª edição em Santa Maria ([USRA](https://py3ur.blogspot.com/)), o desenvolvedor [Nataniel Kegles (PY3NT)](https://www.kegles.com.br/contrate) resolveu **doar e abrir o código** no formato *copyleft*: qualquer pessoa pode copiar e modificar sem pagar royalties, mantendo apenas os **créditos originais** no rodapé da aplicação.

A stack utilizada é:
- [PHP](https://php.net/)  
- [Laravel](https://laravel.com/)  
- [MariaDB](https://mariadb.org/) (ou MySQL)  

---

## Instalação

### 1. Configuração do `.env`
Adapte as variáveis de ambiente:

```env
# PIX
RANCHO_PIX_KEY="as23f3"                              # chave pix (aleatória) que receberá os pagamentos
RANCHO_PIX_NAME="70 RANCHO DO RADIOAMADOR GAUCHO"    # nome da edição (sem caracteres especiais)
RANCHO_MERCHANT_CITY="SANTA MARIA"                   # cidade da edição (sem caracteres especiais)
RANCHO_ORG_PASSWORD=senhateste                       # senha para acesso ao painel de administração

# App
APP_URL=http://rancho.com.br                         # host da aplicação

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rancho
DB_USERNAME=root
DB_PASSWORD=
DB_SOCKET=
```

### 2. Execute no terminal:

```shell
chmod -R 777 storage/
php artisan migrate
php artisan db:seed
php artisan config:clear
```

## Licença

O SOFTWARE É FORNECIDO "TAL COMO ESTÁ", SEM GARANTIA DE QUALQUER TIPO, EXPRESSA OU IMPLÍCITA, INCLUINDO MAS NÃO SE LIMITANDO ÀS GARANTIAS DE COMERCIALIZAÇÃO, CONVENIÊNCIA PARA UM PROPÓSITO ESPECÍFICO E NÃO INFRAÇÃO. EM NENHUMA SITUAÇÃO DEVEM AUTORES(AS) OU TITULARES DE DIREITOS AUTORAIS SEREM RESPONSÁVEIS POR QUALQUER REIVINDICAÇÃO, DANO OU OUTRAS RESPONSABILIDADES, SEJA EM AÇÃO DE CONTRATO, PREJUÍZO OU OUTRA FORMA, DECORRENTE DE, FORA DE OU EM CONEXÃO COM O SOFTWARE OU O USO OU OUTRAS RELAÇÕES COM O SOFTWARE.

Este software é distribuido sob licença [MIT](https://opensource.org/license/mit)
