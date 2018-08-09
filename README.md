# Iugu Magento 1.9

Módulo para adicionar as formas de pagamento da Iugu ao Magento versão 1.9

## Pré requisitos

* Instalação do Magento 1.9. O módulo pode funcionar em versões anteriores do Magento, mas não foi testado em nenhuma outra além da 1.9. Ele não funciona no Magento 2.x.

* [One Step Checkout 6 Brasil Pro](http://onestepcheckout.com.br) - plugin que cria campos e faz validações necessárias no Brasil, como escolha entre pessoa física ou jurídica, campos de CPF / CNPJ, CEP, e outros, no cadastro do cliente.

* Não obrigatório: [Tradução do Magento 1.9 para português](https://mariosam.com.br/magento/traducao-magento/) - pacote de tradução para pt_BR bastante completo, que traduz inclusive a interface administrativa do Magento. Os nomes de menus mencionados neste documento assumem que este pacote seja instalado. Se ele não for, provavelmente seus menus vão exibir textos diferentes dos mencionados aqui.

## Instalação

* *Certifique-se de testar antes em ambiente de desenvolvimento, nunca instale diretamente em ambiente de produção.*

* Se sua loja ainda não usa o [One Step Checkout 6 Brasil Pro](http://onestepcheckout.com.br), faça a instalação, seguindo as instruções deles. IMPORTANTE: para que a configuração do OSC6 se comporte como descrito, certifique-se que o admin do Magento está no idioma Português (Brasil).

* (Opcional) Baixe e instale o pacote de tradução para o Magento 1.9, encontrado no endereço [https://mariosam.com.br/magento/traducao-magento/](https://mariosam.com.br/magento/traducao-magento/)

* Instale o plugin [Iugu Magento](https://github.com/iugu/iugu-magento) baixando o zip ou clonando este repositório: 

        git clone https://github.com/iugu/iugu-magento

* Copie o conteúdo do repositório / zip para a raiz da instalação do Magento. Certifique-se de estar fazendo a cópia corretamente, as pastas do plugin correspondem a algumas pastas do Magento, e elas serão mergeadas durante a cópia.

* Limpe os caches do Magento.

## Configuração

* Confirme se a extensão está ativa, navegando para Sistema > Configuração > Avançado > Avançado.

* Expanda a lista *Desligar Módulos*, e verifique se o módulo Inovarti_Iugu está habilitado. Em caso negativo, habilite-o.

* Navegue para Sistema > Configuração > Vendas > Formas de pagamento, e na lista devem aparecer 3 itens referentes à Iugu: as formas de pagamento da Iugu: Configuração, Boleto e Cartão de crédito. Os parâmetros são auto-explicativos, mas segue abaixo uma descrição detalhada de cada um:

* Em *Configuração*, informe os seguintes parâmetros:
  * Ambiente: selecione qual ambiente você deseja utilizar (Teste ou Produção)
  * ID da conta: seu ID de cliente Iugu
  * API token: token que você gerou no seu painel Iugu

* Em *Boleto*, informe os seguintes parâmetros:
  * Ativado: ativa ou desativa o pagamento via boleto
  * Título: título que deve aparecer durante o checkout
  * Status do novo pedido: status que o pedido deve assumir quando for pago via boleto
  * Instruções: instruções a serem impressas no boleto
  * Ordem de clasificação: inteiro que indica em que posição esta forma de pagamento vai aparecer no checkout 

* Em *Cartão de crédito*, informe os seguintes parâmetros:
  * Ativado: ativa ou desativa o pagamento via cartão de crédito
  * Título: título que deve aparecer durante o checkout
  * Status do novo pedido: status que o pedido deve assumir quando for pago via cartão de crédito
  * Tipos de cartão de crédito: habilita ou desabilita individualmente as bandeiras de cartão de crédito no checkout
  * Número máximo de parcelas: limite de parcelas que o checkout vai permitir
  * Valor mínimo por parcela: limita o parcelamento de forma a não permitir parcelas menores do que este valor
  * Juros: permite criar percentuais de juros mensais para cada número de parcelas 
  * Ordem de clasificação: inteiro que indica em que posição esta forma de pagamento vai aparecer no checkout 


