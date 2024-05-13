# BNCC

## Sobre
A biblioteca `BNCC` oferece uma interface programática para acessar informações da Base Nacional Comum Curricular do Brasil. Com esta biblioteca, desenvolvedores podem integrar informações da BNCC em suas aplicações educacionais, facilitando o acesso a dados curriculares padronizados em todo o território nacional.

## Instalação

### Requisitos
- PHP 8.0 ou superior

## Uso
Aqui está um exemplo básico de como usar a biblioteca para consultar informações da BNCC:

```php
require_once 'vendor/autoload.php';

use AdrianoHCampos\BNCC\BNCC;

$bncc = new BNCC();

// Exemplo de como buscar os dados
$anos = [1, 2, 3];
$campos = [1, 2, 3, 4, 5]
$resultado = $bncc->buscaDados('infantil', [1, 2, 3], [1, 2, 3, 4, 5]);

print_r($resultado);

```

## Contribuindo
Contribuições são bem-vindas!

## Licença
Este projeto está licenciado sob a Licença MIT

## Contato
Se você tiver qualquer dúvida ou sugestão, sinta-se à vontade para abrir uma issue aqui no GitHub ou entrar em contato diretamente pelo email adrianohcampos@gmail.com