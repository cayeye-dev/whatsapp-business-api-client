<?php

namespace Cayeye\WhatsAppBusinessApiClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://developers.facebook.com/docs/whatsapp/api/reference
 *      https://developers.facebook.com/docs/whatsapp/api/errors
 */
class WhatsAppBusinessApiClient extends AbstractWhatsAppBusinessApiClient
{
    public function getProfilePhoto($binary = false): ResponseInterface
    {
        $query = [
            'format' => 'link',
        ];

        if ($binary) {
            unset($query['format']);
        }

        return $this->request('GET', '/v1/settings/profile/photo', $query);
    }

    public function uploadProfilePhoto(string $rawData): ResponseInterface
    {
        $options = [
            'body' => $rawData,
        ];

        return $this->request('POST', '/v1/settings/profile/photo', $options);
    }

    public function deleteProfilePhoto(): ResponseInterface
    {
        return $this->request('DELETE', '/v1/settings/profile/photo');
    }

    public function getProfileAbout(): ResponseInterface
    {
        return $this->request('GET', '/v1/settings/profile/about');
    }

    public function updateProfileAbout(string $text): ResponseInterface
    {
        $data = [
            'text' => $text,
        ];

        return $this->request('PATCH', '/v1/settings/profile/about', ['json' => $data]);
    }

    public function getContacts(array $contacts): ResponseInterface
    {
        $data = [
            'contacts' => $contacts,
            'blocking' => 'wait',
        ];

        return $this->request('POST', '/v1/contacts', ['json' => $data]);
    }

    public function sendMessage(string $to, string $text): ResponseInterface
    {
        $options = [
            'text' => ['body' => $text],
            # 'preview_url' => true, #:TODO: add check if text has url
        ];

        $data = $this->buildMessageData($to, 'text', $options);

        return $this->request('POST', '/v1/messages', ['json' => $data]);
    }

    #string $providerName = null,
    public function sendMessageWithMedia(string $to, string $type, string $mediaIdOrUrl, string $text = null): ResponseInterface
    {
        if (!in_array($type, ['audio', 'image', 'video', 'document'])) {
            throw new \Exception('unknown type for media, available types are: audio|image|video|document');
        }

        $isHttp = 0 === strpos($mediaIdOrUrl, 'http');

        $optionsType = [
            ($isHttp ? 'link' : 'id') => $mediaIdOrUrl,
        ];

        if (!empty($text)) {
            $optionsType['caption'] = $text;
        }

        #if (null !== $providerName && $isHttp) {
        #    $optionsType['provider'] = ['name' => $providerName];
        #}

        $data = $this->buildMessageData($to, $type, [$type => $optionsType]);

        return $this->request('POST', '/v1/messages', ['json' => $data]);
    }

    public function sendMessageWithDocument(string $to, string $mediaIdOrUrl, string $fileName, string $text = null): ResponseInterface
    {
        $isHttp = 0 === strpos($mediaIdOrUrl, 'http');

        $optionsType = [
            ($isHttp ? 'link' : 'id') => $mediaIdOrUrl,
            'filename'                => $fileName,
        ];

        if (!empty($text)) {
            $optionsType['caption'] = $text;
        }

        $data = $this->buildMessageData($to, 'document', ['document' => $optionsType]);

        return $this->request('POST', '/v1/messages', ['json' => $data]);
    }

    /**
     * @param string       $to
     * @param string       $name
     * @param string|array $phones
     *
     * @return ResponseInterface
     */
    public function sendMessageWithContact(string $to, string $name, $phones): ResponseInterface
    {
        if(is_scalar($phones)) {
            $phones = [$phones];
        }
        if(!is_array($phones)) {
            throw new \Exception('argument phone must be a string or array');
        }

        $contact = [
            "name"   => [
                "formatted_name" => $name,
                #"first_name"     => "<Contact's First Name>",
                #"last_name"      => "<Contact's Last Name>",
            ],
            "phones" => array_map(
                function (string $phone) {
                    return [
                        'phone' => $phone,
                        #'type' => "<Contact's Phone Number Type>",
                    ];
                },
                $phones
            ),
        ];

        $data = $this->buildMessageData($to, 'contacts', ['contacts' => [$contact]]);

        return $this->request('POST', '/v1/messages', ['json' => $data]);
    }

    public function sendMessageWithLocation(string $to, string $lat, string $long, string $name, string $address): ResponseInterface
    {
        $optionsType = [
            'latitude'  => $lat,
            'longitude' => $long,
            'name'      => $name,
            'address'   => $address,
        ];

        $data = $this->buildMessageData($to, 'location', ['location' => $optionsType]);

        return $this->request('POST', '/v1/messages', ['json' => $data]);
    }

    private function buildMessageData(string $to, string $type, array $options)
    {
        $isGroup = false !== strpos($to, '-'); #:TODO: idendify

        return $options + [
                'to'             => $to,
                'type'           => $type,
                'recipient_type' => $isGroup ? 'group' : 'individual',
            ];
    }

    public function readMessage(string $id): ResponseInterface
    {
        return $this->request('GET', sprintf('/v1/messages/%s', $id));
    }

    public function getMedia(string $id): ResponseInterface
    {
        return $this->request('GET', sprintf('/v1/media/%s', $id));
    }

    public function deleteMedia(string $id): ResponseInterface
    {
        return $this->request('DELETE', sprintf('/v1/media/%s', $id));
    }

    public function uploadMedia(string $binary, string $mimeType): ResponseInterface
    {
        $options = [
            'headers' => ['content-type' => $mimeType],
            'body'    => $binary,
        ];

        return $this->request('POST', '/v1/media', $options);
    }

    public function getGroups(): ResponseInterface
    {
        return $this->request('GET', '/v1/groups');
    }

    public function createGroup(string $name): ResponseInterface
    {
        $data = [
            'subject' => $name,
        ];

        return $this->request('POST', '/v1/groups', ['json' => $data]);
    }

    public function updateGroup(string $groupId, string $name): ResponseInterface
    {
        $data = [
            'subject' => $name,
        ];

        return $this->request('PUT', sprintf('/v1/groups/%s', $groupId), ['json' => $data]);
    }

    public function getGroup(string $groupId): ResponseInterface
    {
        return $this->request('GET', sprintf('/v1/groups/%s', $groupId));
    }

    public function getGroupIcon(string $groupId, $binary = false): ResponseInterface
    {
        $query = [
            'format' => 'link',
        ];

        if ($binary) {
            unset($query['format']);
        }

        return $this->request('GET', sprintf('/v1/groups/%s/icon', $groupId), $query);
    }

    public function uploadGroupIcon(string $groupId, string $binary, string $mimeType): ResponseInterface
    {
        $options = [
            'headers' => ['content-type' => $mimeType],
            'body'    => $binary,
        ];

        return $this->request('POST', sprintf('/v1/groups/%s/icon', $groupId), $options);
    }

    public function getGroupInvite(string $groupId): ResponseInterface
    {
        return $this->request('GET', sprintf('/v1/groups/%s/invite', $groupId));
    }

    public function deleteGroupIcon(string $groupId): ResponseInterface
    {
        return $this->request('DELETE', sprintf('/v1/groups/%s/icon', $groupId));
    }

    public function deleteGroupInvite(string $groupId): ResponseInterface
    {
        return $this->request('DELETE', sprintf('/v1/groups/%s/invite', $groupId));
    }

    public function leaveGroup(string $groupId): ResponseInterface
    {
        return $this->request('POST', sprintf('/v1/groups/%s/leave', $groupId));
    }

    public function removeGroupParticipants(string $groupId, array $waIds): ResponseInterface
    {
        $data = [
            'wa_ids' => $waIds,
        ];

        return $this->request('DELETE', sprintf('/v1/groups/%s/participants', $groupId), ['json' => $data]);
    }

    public function addGroupAdmins(string $groupId, array $waIds): ResponseInterface
    {
        $data = [
            'wa_ids' => $waIds,
        ];

        return $this->request('PATCH', sprintf('/v1/groups/%s/admins', $groupId), ['json' => $data]);
    }

    public function removeGroupAdmins(string $groupId, array $waIds): ResponseInterface
    {
        $data = [
            'wa_ids' => $waIds,
        ];

        return $this->request('DELETE', sprintf('/v1/groups/%s/admins', $groupId), ['json' => $data]);
    }

    public function getSettings(): ResponseInterface
    {
        return $this->request('GET', '/v1/settings/application');
    }

    public function updateSettings(array $settings): ResponseInterface
    {
        return $this->request('PATCH', '/v1/settings/application', ['json' => $settings]);
    }

    public function updateWebhook(string $url): ResponseInterface
    {
        return $this->updateSettings(['webhooks' => ['url' => $url]]);
    }

    public function getHealth(): ResponseInterface
    {
        return $this->request('GET', '/v1/health');
    }
}