<?php

namespace DoutorFinancas\MicrosoftGraphEmail\Services;

use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftAuthToken;
use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftFolder;
use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftFolderCollection;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class MicrosoftEmailService
{
    /**
     * @var MicrosoftAuthToken
     */
    protected $token;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $url = 'https://graph.microsoft.com/v1.0/';

    /**
     * @param MicrosoftAuthToken $token
     * @param ClientInterface $httpClient
     * @param string $url default 'https://graph.microsoft.com/v1.0/'
     */
    public function __construct(
        MicrosoftAuthToken $token,
        ClientInterface    $httpClient,
        string             $url = 'https://graph.microsoft.com/v1.0/'
    ) {
        $this->token = $token;
        $this->httpClient = $httpClient;
        $this->url = $url;
    }

    public function getMessages(string $mailbox, bool $useImmutableIds = false): array
    {
        $token = $this->token->getTokenString();

        $headers = ['Content-Type' => 'application/json'];

        if ($useImmutableIds) {
            $headers['Prefer'] = "IdType=\"ImmutableId\"";
        }

        $messageList = json_decode(
            $this->sendGetRequest(
                $this->url . 'users/' . $mailbox . '/mailFolders/Inbox/Messages',
                $token,
                $headers
            )
        );
        if (isset($messageList->error) && $messageList->error) {
            throw new \Exception($messageList->error->code . ' ' . $messageList->error->message);
        }
        $messageArray = [];

        foreach ($messageList->value as $mailItem) {
            $attachments = $this->handleMailAttachments($mailbox, $mailItem->id, $token);

            $messageArray[] = [
                'id' => $mailItem->id,
                'sentDateTime' => $mailItem->sentDateTime,
                'subject' => $mailItem->subject,
                'bodyPreview' => $mailItem->bodyPreview,
                'importance' => $mailItem->importance,
                'conversationId' => $mailItem->conversationId,
                'isRead' => $mailItem->isRead,
                'body' => $mailItem->body,
                'sender' => $mailItem->sender,
                'toRecipients' => $mailItem->toRecipients,
                'ccRecipients' => $mailItem->ccRecipients,
                'toRecipientsBasic' => $this->basicAddress($mailItem->toRecipients),
                'ccRecipientsBasic' => $this->basicAddress($mailItem->ccRecipients),
                'replyTo' => $mailItem->replyTo,
                'attachments' => $attachments,
            ];
        }

        return $messageArray;
    }

    public function getMailFolders($mailbox): MicrosoftFolderCollection
    {
        return $this->getMailIteration(
            new MicrosoftFolderCollection(),
            $this->url . 'users/' . $mailbox . '/mailFolders',
            $this->token->getTokenString()
        );
    }


    public function getMailFolderIdByName($mailbox, $name): ?MicrosoftFolder
    {
        return $this->getMailFolders($mailbox)->findByName($name);
    }

    public function moveEmail($mailbox, $id, $folder): array
    {
        $token = $this->token->getTokenString();

        $url = $this->url . 'users/' . $mailbox . '/messages/' . $id . '/move';

        return json_decode($this->sendPostRequest(
            $url,
            sprintf('{ "destinationId": "%s" }', $folder),
            $token,
            ['Content-Type' => 'application/json']
        ), true);
    }

    protected function getMailIteration(
        MicrosoftFolderCollection $collection,
        string                    $url,
        string                    $token
    ): MicrosoftFolderCollection {
        $list = json_decode(
            $this->sendGetRequest(
                $url,
                $token,
                ['Content-Type' => 'application/json']
            ),
            true
        );

        foreach ($list['value'] as $folder) {
            if (!is_array($folder)) {
                continue;
            }

            $collection->add(
                new MicrosoftFolder($folder['id'], $folder['displayName'])
            );
        }

        if (isset($list['@odata.nextLink'])) {
            $collection = $this->getMailIteration(
                $collection,
                $list['@odata.nextLink'],
                $token
            );
        }

        return $collection;
    }

    protected function handleMailAttachments($mailbox, $mailId, $token): array
    {
        $attachments = (json_decode(
            $this->sendGetRequest(
                $this->url . 'users/' . $mailbox . '/messages/' . $mailId . '/attachments',
                $token,
                ['Content-Type' => 'application/json']
            )
        ))->value;

        if (count($attachments) < 1) {
            return [];
        }

        foreach ($attachments as $attachment) {
            if ($attachment->{'@odata.type'} == '#microsoft.graph.referenceAttachment') {
                // @TODO need to implement getting stuffs from SharePoint
                $attachment->contentBytes = base64_encode('This is a link to a SharePoint online file, not yet supported');
                $attachment->isInline = 0;
            }
        }

        return $attachments;
    }

    protected function basicAddress($addresses): array
    {
        $ret = [];

        foreach ($addresses as $address) {
            $ret[] = $address->emailAddress->address;
        }

        return $ret;
    }

    protected function sendGetRequest($url, $token, $headers = []): string
    {
        try {
            $request = new Request(
                'GET',
                $url,
                array_merge([
                    'Authorization' => 'Bearer ' . $token,
                ], $headers)
            );

            $response = $this->httpClient->sendRequest($request);

            return $response->getBody()->getContents();
        } catch (ClientExceptionInterface $e) {
            echo $e->getMessage() . PHP_EOL;
            return '';
        }
    }

    public function sendPostRequest($url, $body, $token, $headers = []): string
    {
        try {
            $request = new Request(
                'POST',
                $url,
                array_merge([
                    'Authorization' => 'Bearer ' . $token,
                ], $headers),
                $body
            );

            $response = $this->httpClient->sendRequest($request);

            return $response->getBody()->getContents();
        } catch (ClientExceptionInterface $e) {
            echo $e->getMessage() . PHP_EOL;
            return '';
        }
    }
}
