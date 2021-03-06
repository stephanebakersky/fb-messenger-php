<?php

namespace pimax\Messages;


/**
 * Class ImageMessage
 *
 * @package pimax\Messages
 */
class ImageMessage extends Message
{
    /**
     * @var null|string
     */
    protected $recipient = null;

    /**
     * @var null|string
     */
    protected $text = null;

    /**
     * @var null|array
     */
    protected $quick_replies = null;

    /**
     * Message constructor.
     *
     * @param string $recipient
     * @param string $file Web Url or local file with @ prefix
     * @param array $quick_replies array of array to be added after attachment
     */

    public function __construct($recipient, $file, $quick_replies = array())
    {
        $this->recipient = $recipient;
        $this->text = $file;
        $this->quick_replies = $quick_replies;
    }

    /**
     * Get message data
     *
     * @return array
     */
    public function getData()
    {
        $res = [
            'recipient' =>  [
                'id' => $this->recipient
            ]
        ];

        $attachment = new Attachment(Attachment::TYPE_IMAGE, [], $this->quick_replies);

        if (strcmp(intval($this->text), $this->text) === 0) {
            $attachment->setPayload(array('attachment_id' => $this->text));
            $res['message'] = $attachment->getData();
        } elseif (strpos($this->text, 'http://') === 0 || strpos($this->text, 'https://') === 0) {
            $attachment->setPayload(array('url' => $this->text));
            $res['message'] = $attachment->getData();
        } else {
            $attachment->setPayload(array('url' => basename($this->text)));
            $attachment->setFileData($this->getCurlValue($this->text, mime_content_type($this->text), basename($this->text)));
            $res['message'] = $attachment->getData();
            $res['filedata'] = $res['message']['filedata'];
            unset($res['message']['filedata']);
        }

        return $res;
    }
}
