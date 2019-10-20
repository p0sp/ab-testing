#!/usr/bin/env bash
set -e

ls -lah


chown www-data:www-data -R public var

$@