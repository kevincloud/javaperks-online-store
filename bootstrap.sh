#!/bin/bash

apachectl stop

export KUBE_TOKEN=$(cat /var/run/secrets/kubernetes.io/serviceaccount/token)
export VAULT_TOKEN=$(curl --request POST \
  --data "{\"jwt\": \"$KUBE_TOKEN\", \"role\": \"cust-api\"}" \
  $VAULT_ADDR/v1/auth/kubernetes/login | jq -r .auth.client_token) && echo $VAULT_TOKEN

echo "export VAULT_TOKEN=$VAULT_TOKEN" > /var/run/secrets/vault_token
source /var/run/secrets/vault_token

apachectl -D FOREGROUND
