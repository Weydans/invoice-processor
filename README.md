# Processador de faturas

Sistema desenvolvido em Laravel utilizando as principais funcionalidades do framework.



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
