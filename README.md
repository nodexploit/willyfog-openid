willyfog-openid
===============

[OpenID Connect](http://openid.net/) server based on [bshaffer/oauth2-server-php](https://github.com/bshaffer/oauth2-server-php) (thank you!) for the willyfog project.

## Deploy

If you came directly here, please see [willyfog](https://github.com/popokis/willyfog)
 for more details about the deployment.

## Give it a try

1. Access: 

`http://openid.willyfog.com/authorize?client_id=testclient&redirect_uri=gihttp://willyfog.com/login/callback&response_type=code&scope=openid&state=xyz`

2. Authorize the request! Try with email `willy@example.com` password `foobar`.

3. Take the `code` param from the query string, and then call the `token` endpoint:

```
curl http://openid.willyfog.com/token -d 'grant_type=authorization_code&client_id=testclient&code=<QUEY_STRING_CODE>&redirect_uri=http://willyfog.com/login/callback'
```

And then you will have your brand new access token like this:

```
{
  "access_token":"e5fc82b8c10356e24bcbe17679ff70c71bde21d7",
  "expires_in":3600,
  "token_type":"Bearer",
  "scope":"openid",
  "id_token":
    "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiIxOTIuMTY4LjMzLjEwIiwic
    3ViIjpudWxsLCJhdWQiOiJ0ZXN0Y2xpZW50IiwiaWF0IjoxNDY4NDQzMjY0LCJleHAiOjE0
    Njg0NDY4NjQsImF1dGhfdGltZSI6MTQ2ODQ0MzI2NH0.pcxhbmsrezlJheJJdG1N8xX8EIb
    nOZNgZQjUoBcjBjfwcBd3HNuzl3sG_b4wWSSzLJon8MydxugE9nFdXT3pED..."
 }
```

Also, you can use the `userInfo` endpoint with the obtained `access_token`:

```
curl -X POST -H "Authorization: Bearer <ACCESS_TOKEN>" http://openid.willyfog.com/userInfo
```