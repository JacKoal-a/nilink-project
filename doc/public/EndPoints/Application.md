# Application API - Nilink

These API shoud be used by the application 

## **Authorization**

All the following endpoint require an authorization token that is retrived by OAuth API

```http
Authorization: Bearer {access_token}
```

## Endpoints

---

### **User info**

Get user information after he logged in your application

```http
GET    /api/application/userinfo
```

#### **Response**

```json
{
    "name": {name},
    "surname": {surname},
    "mail": {mail}
}
```

> *name*:    string
> 
> *surname*:    string
> 
> *mail*:    string

---

### Get application's data

Get application' data perviously saved

```http
GET    /api/application/data
```

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

### Set application's data

Set application' data perviously saved

```http
POST    /api/application/data
```

#### Parameters

```json
{data}
```

> *data*:    JSON

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
GET    /api/application/data
```

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

### Set application's data

Set users' data perviously saved

```http
POST    /api/application/data
```

#### Parameters

```json
{data}
```

> *data*:    JSON

#### **Response**

```json
{
    "message": {msg}
}
```

> *msg*:    string
