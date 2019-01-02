#!/usr/bin/env bash

java -jar swagger-codegen-cli-2.3.1.jar generate \
    -i telegram.yaml \
    -l php \
    --invoker-package \Telegram \
    -o ././../src/Api/Telegram

java -jar swagger-codegen-cli-2.3.1.jar generate \
    -i phpythagoras.yml \
    -l php \
    --invoker-package \PHPythagoras \
    -o ././../src/Api/PHPythagoras

java -jar openapi-generator-cli-3.3.3.jar generate \
    -i openweathermap.yml \
    -l php \
    --invoker-package \OpenWeather \
    -o ././../src/Api/OpenWeather