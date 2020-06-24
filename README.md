# Development environment

## First time setup

1. Add `~/.ssh/id_rsa.pub` to [GitLab](https://git.cpadev.com/profile/keys)
2. Authorize in docker registry (`docker login registry.cpadev.com:4567`)
3. Create public network and start nginx-proxy container (see below)
4. `php -r "file_exists('.env') || copy('.env.example', '.env');"`
5. On OSs with low `max_map_count` (< 262144) run
`sudo sysctl -w vm.max_map_count=262144` (for elastic)
6. `php artisan key:generate`
7. `make`

## Code Style
Code quality is protected by GrumPHP. GrumPHP checks code style (__PSR2__) and tests. It runs before every commit via git hooks and wont let you commit bad smell code. To force GrumPHP to allow to commit add `-n` or `--no-verify` flag to commit command (__not recommended!__).

To enable php code style checks in PhpStorm open settings, go to _Languages & Frameworks_ -> _PHP_ -> _Code Sniffer_ and configure _PHP Code Sniffer path_ to `vendor/bin/phpcs`. Then go to _Editor_ -> _Inspections_ -> _Quality tools_ -> _PHP Code Sniffer_ validation and select __PSR2__ for _Coding standard_.

## Setup network for host access

```bash
docker network create -d bridge --subnet 10.254.254.0/24 --gateway 10.254.254.254 dockerhost
```
For MacOS you can use host alias `host.docker.internal`

## Setup and run docker nginx-proxy

```bash
docker network create public
docker run --detach --restart=always \
  --name global_nginx \
  --publish 80:80 \
  --network="public" \
  --volume /var/run/docker.sock:/tmp/docker.sock:ro \
  jwilder/nginx-proxy
```

For HTTPS support also publish 443 port and map certs volume to `/etc/nginx/certs`.
For certs auto-generation use letsencrypt (https://git.cpadev.com/cpadev/proxy).

## ElasticSearch

### Setup search indexc

```bash
php artisan elastic:create-index "App\Models\Product\ProductCategoryIndexConfigurator"
php artisan elastic:create-index "App\Models\Product\ProductIndexConfigurator"
php artisan elastic:create-index "App\Models\OrderIndexConfigurator"
php artisan elastic:create-index "App\Models\UserIndexConfigurator"
php artisan products:elastic
php artisan scout:import "\App\Models\Product\Category"
php artisan scout:import "\App\Models\Order"
php artisan scout:import "\App\Models\User"
php artisan queue:work
```

### test queries:

1. Verify not indexing html tags


```json
{
    "query": {
        "bool": {
            "must": [{
                "query_string": {
                    "default_field": "description",
                    "query": "span"
                }
            }]
        }
    },
    "from": 0,
    "size": 10
}
```
```bash
curl -X POST \
  http://localhost:9200/product_index/_search?pretty \
  -H 'Accept: application/json, text/javascript, */*; q=0.01' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/json' \
  -d '{"query":{"bool":{"must":[{"query_string":{"default_field":"description","query":"span"}}],"must_not":[],"should":[]}},"from":0,"size":10,"sort":[],"aggs":{}}'


```

## REST API

### Generation

```bash
php artisan l5-swagger:generate
```

### Access

Generated documentation would be available at [/api/docs](http://dropwow.loc/api/docs).

### Monitoring
1. Indices stats
    ```
    http://localhost:9200/_cat/indices?v
    ```
2. Redis queue monitor
    ```bash
    docker exec -t dw-redis redis-cli monitor
    ```
3. Elastic Dashboard

    To run ElasticHead, install this chrome extension:
    ```
    https://chrome.google.com/webstore/detail/elasticsearch-head/ffmkiejjmecolpfloofpjologoblkegm
    ```
    and click on it's icon in the extensions bar.

## Command-line aliases
For zsh add the following to the file `~/.zshrc`:
 
```bash
alias dc="docker-compose -f docker-compose.yml"
alias dcdev="docker-compose -f docker-compose.yml -f docker-compose-dev.yml"
```

So, `dc up -d` will start the project and `dcdev up -d` will do the same with xdebug enabled.

## Queue

To run processing all jobs worker:
```bash
php artisan queue:work
```

You can specify with queue to run, and number of retries:
```bash
php artisan queue:work --queue=cscart --tries=1

```

### Availible queues
 - ```cscart```

    Jobs for importing products from cscart api to dropwow.


### Profiling


Signup at blackfire.io to get access tokens

Fill them, by copying ENV params from .env.blackfire to .env

Install chrome extension to start profiling:
[blackfire extension](https://chrome.google.com/webstore/detail/blackfire-companion/miefikpgahefdbcgoiicnmpbeeomffld)

Start profiling application by run 
```bash
make start_fire
```


