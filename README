# Backend em Wordpress

## Introdução

Este tema WordPress foi desenvolvido para criar uma estrutura de backend que serve conteúdo via API REST no formato JSON. Ele é especialmente útil para projetos que necessitam de um headless CMS, onde o frontend é desacoplado e consome os dados através de endpoints REST.

```
theme-folder/
├── functions.php      # Lógica principal do tema
├── acf-json/         # Configurações dos campos ACF
├── style.css         # Arquivo de estilo principal
```

## Objetivo

* Fornecer uma estrutura organizada para gerenciamento de conteúdo;
* Disponibilizar os dados através de endpoints API REST bem definidos;
* Facilitar a integração com frontends modernos (React, Vue, Angular, etc.);
* Permitir o cadastro e gerenciamento de usuários via API.

## Funcionalidades

* `GET /wp-json/json_bloco/v1/conteudo`: Retorna todo o conteúdo configurado no painel;
* `POST /wp-json/json_bloco/v1/users`: Cadastra um novo usuário;
* `GET /wp-json/json_bloco/v1/users`: Lista usuários cadastrados (com paginação).

## Estrutura de conteudo

O tema organiza os conteúdos na seguintes seções:

* **Header**: título, subtítulo, botão de scroll (ícone), imagem de pattern;
* **Menu**: logo, botão de voltar e até 4 categorias;
* **Cards com imagens**: título, descrição e até 4 cards com imagem, título e descrição;
* **Album**: título, descrição e até 3 imagens;
* **App**: título e descrição;
* **Tags**: título e lista com até 15 tags;
* **Cards de texto**: até 3 cards com título e descrição;
* **Formulário**: título, subtítulo, informação adicional, imagem e até 4 campos com nome, placeholder e tipo;
* **Rodapé**: título e até 4 links;
* **Usuários**: sistema de cadastro via API com campos de nome, e-mail, idade, cidade e data de cadastro;

## Como utilizar este tema?

Para usar  este tema você precisa seguir os passos abaixo:

1 - Para usar o tema criado é necessário criar um site local no Wordpress. Para isso você pode baixar o [Local WP](https://localwp.com/);

2 - Em seguida, clique em "Add Local site";

3 - Selecione a opção "Create a new site" e clique em 'Continue";

4 - Dê um nome ao se site e clique em "Continue";

5 - Você pode manter as opções padrão de ambiente ou configurar opções personalizadas e clique em "Continue";

6 - Crie um nome de usuário, uma senha e clique em "Criar site";

7 - Acesse o "WP Admin" e na aba lateral instale os seguintes plugins: "Advanced Custom Fields" e "ACF to Rest API";

8 - Em seguida, no aplicativo no Local WP clique em "Site folder" para abrir a pasta onde o site foi criado localmente e busque por "app\public\wp-content\themes" (Caso crie o site de outra forma, você deve buscar na localmente pela pasta onde ficam armazenados os temas);

9 - Dentro dessa pasta, faça um clone deste repositório com o comando:
```bash
git clone <Nome-do-repositório>
```

10 - No painel administrativo do Wordpress vá em "Aparência/ Temas" e ative o tema do backend";

11 - Com ele ativado, você habilitará uma aba chamada "Blocos Json",nela clique em "Add Post" dê o nome "Content" e preencha os campos;

12 - Em seguida, clique em "Save draft" e em "Publish".

13 - Para acessar o json gerado você deve usar o link "http://<seu site>.local/wp-json/json_bloco/v1/conteudo";

## Tecnologias e Plugins utilizados

* **Advanced Custom Fields (ACF)**: Para criação dos campos personalizados;
* **ACF to Rest API**: Para expor os campos do ACF via API REST;
* **WordPress 5.0+**;
* **PHP 7.4+**;
* **API REST do WordPress**;
* **JSON para estrutura de dados**;