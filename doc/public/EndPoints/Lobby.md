# Lobby API - Nilink

## **Authorization**

All the following endpoint require an authorization token that is retrived by OAuth API

```http
Authorization: Bearer {access_token}
```

## Endpoints

---

### **Create**

Get user information after he logged in your application

```http
GET    /api/lobby/create
```

#### Parameters

```json
{
    "name": {name}
}
```

> *name*:    string



#### **Response**

```json
{
    "message": "Successfully retrived lobby&#39;s member",
    "data": [
        {
            "nickname": {nickname},
            "status": {status},
            "verbose_status": {verbose_status}
        },
        {...}
    ]
}
```

> *nickname*:    string
> 
> *status*:    bool
> 
> *verbose_status*:    string

---

### **Members**

Get user information after he logged in your application

```http
GET    /api/lobby/{lobby_id}/members
```

#### Parameters

> *lobby_id*:    integer

#### **Response**

```json
{
    "message": "Successfully retrived lobby's member",
    "data": [
        {
            "nickname": {nickname},
            "status": {status},
            "verbose_status": {verbose_status}
        },
        {...}
    ]
}
```

> *nickname*:    string
> 
> *status*:    bool
> 
> *verbose_status*:    string

---

### **Listen**

Retrive all new messages sent in a lobby since you have joined it, ordered by times

```http
GET    /api/lobby/{lobby_id}/listen
```

#### Parameters

> *lobby_id*:    integer

#### **Response**

```json
{
    "messages": [
        {
            "from":{
                "user_id":{user_id},
                "nickname":{nickname}
            },
            "content":{content}
            "timestamp":{timestamp}
        },
        {...}
    ]
}
```

> *user_id_*:    string
> 
> *nickname*:    string
> 
> *content*:    JSON
> 
> timestamp: string

---

### **Send**

Send a message to all the other users that has joined the lobby

```http
GET    /api/lobby/{lobby_id}/send
```

#### Parameters

```json
{data}
```

> *data*:    JSON
> 
> *lobby_id*:    integer

#### **Response**

```json
{
    "message": "Successfully retrived lobby&#39;s member",
    "data": [
        {
            "nickname": {nickname},
            "status": {status},
            "verbose_status": {verbose_status}
        },
        {...}
    ]
}
```

> *nickname*:    string
> 
> *status*:    bool
> 
> *verbose_status*:    string

---

### Get lobby' s data

Get lobby's data perviously saved

```http
GET    /api/lobby/{lobby_id}/data
```

#### Parameters

> *lobby_id*:    integer

#### **Response**

```json
{
    "message": {msg},
    "data": {data}
}
```

> *msg*:    string
> 
> *data*:    JSON

---

### Set lobby's data

Set application' data

```http
POST    /api/lobby/{lobby_id}/data
```

#### Parameters

```json
{data}
```

> *data*:    JSON
> 
> *lobby_id*: integer

#### **Response**

```json
{
    "message": {msg}
}
```

> *msg*:    string

---

### Get user 's data

Get user's data perviously saved

```http
GET    /api/lobby/{lobby_id}/data
```

#### Parameters

> *lobby_id*:    integer

#### **Response**

```json
{
    "message": {msg},
    "data": {data}
}
```

> *msg*:    string
> 
> *data*:    JSON

---

### Set lobby's data

Set users' data perviously saved

```http
POST    /api/lobby/{lobby_id}/data
```

#### Parameters

```json
{data}
```

> *data*:    JSON
> 
> *lobby_id*: integer

#### **Response**

```json
{
    "message": {msg}
}
```

> *msg*:    string
