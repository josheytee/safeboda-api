# Promo Code API Documentation
Api documentation for Event Promo Codes generation and retrieval.

# Allowed HTTPs requests:
<pre>
POST     : To create resource 
PUT      : Update resource
GET      : Get a resource or list of resources
</pre>

# Description Of Usual Server Responses: 
- 200 `OK` - the request was successful (some API calls may return 201 instead).
- 201 `Created` - the request was successful and a resource was created.
- 404 `Not Found` - resource was not found.
- 422 `Unprocessable Entity` - the request was well formed but was unable to be followed due to validation/semantic errors.

## Promo Codes
### Promo Codes Collection [/api/coupons(?type)]
#### GET | List promo codes
Retrieve promo codes (all or active only).

+ Parameters
    + type (optional, String): Set type = all to retrieve all promo codes including expired and inactive codes. Set type = active to retrieve only active promo codes. Not assigning any value to type also returns only active promo codes.

+ Response (application/json)

[type = "active"]
```json
    {
        "promoCodes":   {
            {
                "id": 1,
                "code": "XAYSDE",
                "radius": 500,
                "amount": 1500,
                "created_at": "2021/11/12",
                "expires_at": "2021/12/12", 
            },
            {
                "id": 2,
                "code": "YBZTEF",
                "radius": 750,
                "amount": 2500,
                "created_at": "2021/11/12",
                "expires_at": "2021/01/01",
            }
        }
    }
```
[type="all"]
```json
{
        "promoCodes":   {
            {
                "id": 1,
                "code": "XAYSDE",
                "radius": 500,
                "amount": 1500,
                "created_at": "2021/11/12",
                "expires_at": "2021/12/12",
                "deleted_at": null
            },
            {
                "id": 2,
                "code": "YBZTEF",
                "radius": 750,
                "amount": 2500,
                "created_at": "2021/11/12",
                "expires_at": "2021/01/01",
                "deleted_at": null
            },
            {
                "id": 3,
                "code": "ZCAUFG",
                "radius": 400,
                "amount": 1000,
                "created_at": "2021/11/12",
                "expires_at": "2021/11/12",
                "deleted_at": null
            },
            {
                "id": 4,
                "code": "ADBVGH",
                "radius": 1000,
                "amount": 3500,
                "created_at": "2021/11/12",
                "expires_at": "2021/01/01",
                "deleted_at": "2021/11/12"
            }
        }
    }
```

### Promo Code Generation [/api/coupons/create]
#### POST | Generate a new Promo Code
+ Parameters
    + radius (required, Number): Radius (in meters) relative to the event venue that the promo code will be valid in.
    + amount (required, Number): Amount of ride promo code is worth.
    + expires_at (required, Date): Expiry date (in `YYYY-MM-DD` format) of the promo code.

+ Request (application/json)
```json
    {
        "radius": 500,
        "amount": 1500,
        "expires_at": "2021/12/12",
    }
```

+ Response 201 (application/json)
```json
    {
        "promoCode": {
            "id": 1,
            "code": "XAYSDE",
            "radius": 500,
            "amount": 1500,
            "created_at": "2021/11/12",
            "expires_at": "2021/12/12", 
        }
    }
```

+ Response 422 (application/json)
```json
    {
        "errors": {
            "radius": {
                "The radius field is required",
                "The radius must be a number"
            },
            "amount": {
                "The ride worth field is required",
                "The ride worth must be a number"
            },
            "expires_at": {
                "The expiry date field is required",
                "The expiry date is not a valid date"
            } 
        }
    }
```

## Promo Code validity & retrieval [/coupons/validate]
A single promo code object with all its details if valid.

### POST | Check validity and retrieve a Promo Code
+ Parameters
    + code (required, String, `AAAAA`): A valid generated promo code .
    + origin (required, Array)
        + lat (required, Number): Latitude position of user's pickup origin
        + lon (required, Number): Longitude position of user's pickup origin
    + destination (required, Array)
        + lat (required, Number): Latitude position of user's destination
        + lon (required, Number): Longitude position of user's destination
+ Request 
```json
    {
        "code": "AAAAA",
        "origin": {
            "lat": 7.7634697,
            "lon": 4.5341617,
        },
        "destination": {
            "lat": 7.760891,
            "lon": 4.5329985,
        } 
    }
```
+ Response 200 (application/json)
```json
    {
        "promoCode": {
            "id": 1,
            "code": "XAYSDE",
            "radius": 500,
            "amount": 1500,
            "created_at": "2021/11/12",
            "expires_at": "2021/12/12", 
        },
        "polyline": "uhkn@oqtZ",
    }
```

+ Response 400 (application/json)
```json
    {
        "error": "Promo code is not valid"
    }
```

### PUT | Configure promo code radius  [/api/coupons/{code}/configure/radius]
Update promo code radius
+ Parameters
    + code (required, String, `AAAAA`): A valid genertaed promo code.
    + radius: (required, Number): New radius (in meters) relative to the event venue that the promo code will be valid in.
+ Request (application/json)
```json
    {
        "radius": 700,
    }
```

+ Response 200 (application/json)
```json
    {
        "promoCode": {
            "id": 1,
            "code": "XAYSDE",
            "radius": 500,
            "amount": 1500,
            "created_at": "2021/11/12",
            "expires_at": "2021/12/12",
        }
    }
```

+ Response 422 (application/json)
    ```json
        {
            "errors": {
                "radius": {
                    "The radius field is required",
                    "The radius must be a number"
                },
            }
        }
    ```
### PUT | Deactivate a Promo Code [/api/coupons/{code}/deactivate]
+ Response 200
```json
    {
        "message": "Promo code deactivated"
    }
```
