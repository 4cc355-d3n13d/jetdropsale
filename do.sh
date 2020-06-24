#!/usr/bin/env sh
if [ -f /.dockerenv ]; then
  command_env="";
else
  command_env="docker exec -it dw-php ";
fi

${command_env} $1
