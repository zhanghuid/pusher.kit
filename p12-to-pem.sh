#!/bin/sh

eg() {
  echo ""
  echo "example: "
  echo "  openssl pkcs12 -in src/Storage/c.p12 -out src/Storage/aps_cert.pem"
}


if [ ! -f "$1" ]; then
    echo "error: input file path can't not be empty"
    eg
    exit
fi

# shellcheck disable=SC2057
if [ -z "$2" ]; then
    echo "error: output path can't not be empty"
    exit
fi


# openssl pkcs12 -in src/Storage/c.p12 -out src/Storage/aps_cert.pem
openssl pkcs12 -in "$1" -out "$2"

