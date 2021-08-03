<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class Mailer
{
    protected $arrMailerConfig;
    protected $mailer;
    protected $email;


    public function __construct($arrMailerConfig, MailerInterface $mailer)
    {
        $this->arrMailerConfig  = $arrMailerConfig;
        $this->mailer           = $mailer;
        $this->email            = $this->emailInit($arrMailerConfig);
    }


    protected function emailInit($arrMailerConfig)
    {
        $email = new TemplatedEmail();

        if( !empty($arrMailerConfig["from"]["address"]) && !empty($arrMailerConfig["from"]["name"]) ) {

            $email->from(new Address($arrMailerConfig["from"]["address"], $arrMailerConfig["from"]["name"]));

        } else if( !empty($arrMailerConfig["from"]["address"]) ) {

            $email->from($arrMailerConfig["from"]["address"]);
        }

        if( !empty($arrMailerConfig["to"]["address"]) && !empty($arrMailerConfig["to"]["name"]) ) {

            $email->to(new Address($arrMailerConfig["to"]["address"], $arrMailerConfig["to"]["name"]));

        } else if( !empty($arrMailerConfig["to"]["address"]) ) {

            $email->to($arrMailerConfig["to"]["address"]);
        }

        return $email;
    }


    public function sendTest()
    {
        return $this->sendStandardEmail(
            "mr.recipient@test.com", "Mr. Recipient",
            "This is a test",
            '@TLIBase/email/test.xml.twig', ["now" => new \DateTime()],
            ["mr.cc@test.com"]
        );
    }


    protected function sendStandardEmail(
        string $toAddress, string $toName, string $subjectUntagged,
        string $templateFilename, array $arrTemplateData,
        array $arrCc = [],
        $fromAddress = null, $fromName = null
    ) {
        $subjectTag         = trim($this->arrMailerConfig["subject"]["tag"]);
        $subjectTagged      = empty($subjectTag) ? $subjectUntagged : ($subjectTag . " " . $subjectUntagged);

        if( !empty($toAddress) && !empty($toName) ) {

            $this->email->to(new Address($toAddress, $toName));

        } else if( !empty($toAddress) ) {

            $this->email->to(new Address($toAddress));
        }

        $this->email
            ->subject($subjectTagged)
            ->htmlTemplate($templateFilename)
            ->context(array_merge($arrTemplateData, [ "emailEnvelopeData" => [
                "toAddress"     => $toAddress,
                "toName"        => $toName,
                "subject"       => [
                    "tag"       => $subjectTag,
                    "untagged"  => $subjectUntagged
                ],
                "fromAddress"   => $fromAddress,
                "fromName"      => $fromName
            ]]));

        if( !empty($arrCc) ) {

            foreach($arrCc as $ccAddress) {

                $this->email->addCc(new Address($ccAddress));
            }
        }

        if( !empty($fromAddress) ) {

            $fromName = empty($fromName) ? $this->arrMailerConfig["from"]["name"] : $fromName;
            $this->email->from(new Address($fromAddress, $fromName));
        }

        return $this->send($this->email);
    }


    protected function send()
    {
        return $this->mailer->send($this->email);
    }
}
