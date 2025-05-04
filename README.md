# Processador de faturas

Sistema desenvolvido em Laravel utilizando as principais funcionalidades do framework.

![Alt text](https://raw.githubusercontent.com/Weydans/invoice-processor/refs/heads/main/public/capa.png)


## Observações do projeto

- Toda configuração de infra realizada com Docker
- Documentação de endpoints com Swagger
- Utilização de arquivo Makefile para facilitar o desenvolvimento e implantação
- Testes automatizados cobrindo quase todas as classes do projeto
- Classes no padrão Single Action Class. Coesas, testáveis e de fácil manutenção
- Infelizmente tive contratempos e não pude terminar o frontend completamente no tempo
- Acreadito ter feito um bom trabalho e desejo uma boa avaliação



## Dependências

É necessário ter previamente instalado em sua máquina os seguintes softwares:

- [git](https://git-scm.com/downloads)
- [Docker](https://docs.docker.com/engine/install/)
- [docker-compose](https://docs.docker.com/compose/install/)

Clique nos links acima para acessar a página de instalação de cada um.



## Instalação

- Clone o projeto
```bash
git clone https://github.com/Weydans/invoice-processor.git
```



## Execução

- Acesse a pasta do projeto
```bash
cd invoice-processor
```


- Suba a plicação com um dos comandos abaixo (`buid` para produção ou apenas `make` para desenvolvimento)
```bash
make build
```

```bash
make
```


- Insira dados fake na base
```bash
make dbseed
```



## Acesso

O acesso pode ser realizado via url `http://localhost:8080/`


## Execução dos testes

- Executa testes de unidade e integração
```bash
make test
```



## Parar Execução

Interrompe a execução dos containers
```bash
make down
```
