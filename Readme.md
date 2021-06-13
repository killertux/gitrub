# Gitrub
(Gently rubbing github for information)

Gitrub scrape for User and Repository data out of github api. It also provides a simple rest API to query the scraped data.

This app architecture was based on the [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html) of Uncle Bob. We can divide it into three layers.

The first one is the Domain layer, where all the business rules are implemented. This code should only change if the requirements change.

The second one is the Gateway Layer. Here is implemented the connection between this service and other ones (ie.: mysql, github rest api). In this layer, we need to implement and follow the contracts that were established by the domain layer.

The third layer is the APP Layer. Here lies the UI and glue logic. This layer is the most volatile.

### Running

To run it first you need to copy `config/env.dev.example.php` to `config/env.dev.php`. Edit this file to add your [github personal access token](https://docs.github.com/en/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token) if you want to. Gitrub can be executed without this credentials but it will be limited in the number of requests that it can do to Github API.

After coping the env file, you can simply execute `docker-compose up` in the root directory.

### CLI

Gitrub provides some console commands. To execute them form inside docker, you can use `docker-compose exec gitrub php /app/bin/console.php`

### Rest API

Gitrub provides a Rest API to query all scraped data. There is a postman resource collection in the resource folder if you want more details.

### GraphQL API

Gitrub also provides a GraphQL API. This API is available over the URI `/graphql`. For more information, you can use a tool like [GraphiQL](https://github.com/graphql/graphiql) or [Altair GraphQL](https://altair.sirmuel.design/).

### Scrapper

The scrapping can be triggered from three places: the Rest API, the GraphQL API and the CLI. Also, there is a cron job that handles automatic scrapping of data.

You can edit the crontab file in `cron/crontab`. Notice that if you change the crontab file, you need to rebuild the docker image before re-upping the services.
You can do this by calling `docker-compose build` and then `docker-compose up` again.

### Running tests

To run all tests you can execute `docker-compose exec gitrub /app/vendor/phpunit/phpunit/phpunit /app/tests`

