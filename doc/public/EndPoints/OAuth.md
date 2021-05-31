# OAuth2.0 - Nilink

## Endpoints

OAuth API endpoints

--------------------------------

### **Authorization**

Get authorization code from user login

```http
GET    /api/oauth/authorization?response_type=code&redirect_uri={redirect_uri}&client_id={client_id}&state={state}&code_challenge={code_challenge}&code_challenge_method={method}
```

#### **URL Parameters**

>     *redirect_uri*:     [YOUR_REDIRECT_URI]
> 
>     *client_id*:     [YOUR_CLIENT_ID]
> 
>     *state*:     [YOUR_STATE]
> 
>     *code_challenge*:     [YOUR_CODE_CHALLENGE]
> 
>     *code_challenge_method*:    S256

#### **Response**

```http
POST    {redirect_uri}?code={authorization_code}&state={state}
```

 ------------------------------------------------------------------------------

### **Token**

Excenge *authorization_code* to get an *access token* that allows your application to use all application's API.

```http
POST    /api/oauth/token?code={authorization_code}&redirect_uri={redirect_uri}&client_id={client_id}&code_verifier={code_verifier}&client_secret={client_secret}&grant_type=authorization_code
```

#### **URL Parameters**

> *authorization_code*:    [YOUR_AUTHORIZATION_CODE]
> 
> *redirect_uri*:    [YOUR_REDIRECT_URI]
> 
> *client_id*:     [YOUR_CLIENT_ID]
> 
> *code_verifier*:     [YOUR_CODE_VERIFIER]
> 
> *client_secret*:     [YOUR_CLIENT_SECRET]

#### **Response**

```json
{
    "access_token" : {access_token},
    "refresh_token" : {refresh_token},
    "expires_in" : {expiration_time},
    "token_type" : "Bearer"
}
```

---------------------------------

### **Refresh Token**

Excenge *refresh token* to get a new pair of access token and refresh token, that allows your application to use all application's API.

```http
GET    /api/oauth/token
```

#### **Post Parameters**

> *grant_type*:   refresh_token
> 
> *refersh_token*:  [YOUR_TOKEN]
> 
> *client_id*:    [YOUR_CLIENT_ID]
> 
> *client_secret*:     [YOUR_CLIENT_SECRET]

#### **Response**

```json
{
    "access_token" : {access_token},
    "refresh_token" : {refresh_token},
    "expires_in" : {expiration_time},
    "token_type" : "Bearer"
}
```
