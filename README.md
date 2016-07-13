willyfog-openid
===============

## Deploy

Install dependencies: `composer install`

Generate public and private keys:

```
openssl genrsa -out data/privkey.pem 4096
openssl rsa -in privkey.pem -pubout -out data/pubkey.pem
```

## Give it a try

Access `http://192.168.33.10/authorize?client_id=testclient&redirect_uri=http://127.0.0.1/login&response_type=code&scope=openid&state=xyz`

Authorize the request!

Take the `code` param from the query string, and then call:

```
curl http://192.168.33.10/token -d 'grant_type=authorization_code&client_id=testclient&code=<QUEY_STRING_CODE>&redirect_uri=http://127.0.0.1/login'
```

And then you will have your brand new access token.