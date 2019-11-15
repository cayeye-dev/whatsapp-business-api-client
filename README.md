# WhatsApp Business Api Client

## Based on
- https://developers.facebook.com/docs/whatsapp/api/reference
- https://developers.facebook.com/docs/whatsapp/api/errors

## Usage
```php
$token = 'YourAdminAuthToken';
$url = 'http://127.0.0.10:3000';
$client = new WhatsAppBusinessApiClient($token, $url);

//set webhook
$client->updateWebhook('https://yourdomain.com/webhook');
```

## Available endpoints

#### Done
```
    //media
    POST	{{URL}}/v1/media
    GET		{{URL}}/v1/media/{{Test-Media-Id}}
    DEL		{{URL}}/v1/media/{{Test-Media-Id}}

    //Settings Application
    GET		{{URL}}/v1/settings/application
    PATCH	{{URL}}/v1/settings/application

    //Messages
    POST	{{URL}}/v1/messages/
    PUT		{{URL}}/v1/messages/<Message ID from Webhook>

    //contacts
    POST	{{URL}}/v1/contacts

    //health
    GET 	{{URL}}/v1/health

    //Settings Profile
    GET		{{URL}}/v1/settings/profile/photo
    POST	{{URL}}/v1/settings/profile/photo
    GET		{{URL}}/v1/settings/profile/about
    PATCH	{{URL}}/v1/settings/profile/about

    //Groups
    GET 	{{URL}}/v1/groups/
    POST	{{URL}}/v1/groups
    GET		{{URL}}/v1/groups/{{Test-Group-Id}}
    PUT		{{URL}}/v1/groups/{{Test-Group-Id}}
    GET		{{URL}}/v1/groups/{{Test-Group-Id}}/icon
    POST	{{URL}}/v1/groups/{{Test-Group-Id}}/icon
    DELETE  {{URL}}/v1/groups/{{Test-Group-Id}}/icon
    GET		{{URL}}/v1/groups/{{Test-Group-Id}}/invite
    DELETE	{{URL}}/v1/groups/{{Test-Group-Id}}/invite
    POST	{{URL}}/v1/groups/{{Test-Group-Id}}/leave
    DELETE	{{URL}}/v1/groups/{{Test-Group-Id}}/participants
    PATCH	{{URL}}/v1/groups/{{Test-Group-Id}}/admins
    DELETE	{{URL}}/v1/groups/{{Test-Group-Id}}/admins
```

#### TODO
```
    //Settings - Backup/Restore
    PATCH	{{URL}}/v1/settings/restore
    PATCH	{{URL}}/v1/settings/backup

    //Settings - Business Profile
    POST	{{URL}}/v1/settings/business/profile
    GET		{{URL}}/v1/settings/business/profile

    //Settings - two step verification
    DEL 	{{URL}}/v1/settings/account/two-step
    POST	{{URL}}/v1/settings/account/two-step

    //Settings - Application
    DEL		{{URL}}/v1/settings/application/media/providers/<Provider Name>
    GET		{{URL}}/v1/settings/application/media/providers
    POST	{{URL}}/v1/settings/application/media/providers
    DEL		{{URL}}/v1/settings/application
    POST	{{URL}}/v1/account/shards

    //Users
    POST     {{URL}}/v1/users
    POST     {{URL}}/v1/users/logout
    POST     {{URL}}/v1/users/login
    GET      {{URL}}/v1/users/{{UserUsername}}
    PUT      {{URL}}/v1/users/{{UserUsername}}
    DELETE   {{URL}}/v1/users/{{UserUsername}}

    //Reg
    POST	{{URL}}/v1/account
    POST	{{URL}}/v1/account/verify

    //support
    GET 	{{URL}}/v1/support

    //stats
    GET 	{{URL}}/v1/stats/db
    GET 	{{URL}}/v1/stats/db/internal
    GET 	{{URL}}/v1/stats/app
    GET 	{{URL}}/v1/stats/app/internal

    //certificates
    POST	{{URL}}/v1/certificates/external
    GET 	{{URL}}/v1/certificates/external/ca
    POST	{{URL}}/v1/certificates/webhooks/ca
    GET 	{{URL}}/v1/certificates/webhooks/ca
    DEL 	{{URL}}/v1/certificates/webhooks/ca
```


