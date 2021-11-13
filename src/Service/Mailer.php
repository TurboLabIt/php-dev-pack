<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class Mailer
{
    protected $email;


    public function __construct(
        protected array $arrMailerConfig,
        protected MailerInterface $mailer
    ) {
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

        // this header tells auto-repliers ("email holiday mode") to not
        // reply to this message because it's an automated email
        $email->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

        return $email;
    }


    public function sendTest()
    {
        return
            $this->buildStandardEmail(
                "mr.recipient@test.com", "Mr. Recipient",
                "This is a test", true,
                '@TLIBase/email/test.html.twig', ["now" => new \DateTime()],
                ["mr.cc@test.com"]
            )
            ->send();
    }


    public function buildStandardEmail(
        string $toAddress, string $toName,
        string $subjectUntagged, bool $addTagToSubject,
        string $templateFilename, array $arrTemplateData,
        array $arrCc = [],
        $fromAddress = null, $fromName = null
    ): static {

        // FROM:
        if( !empty($fromAddress) ) {

            $fromName = empty($fromName) ? $this->arrMailerConfig["from"]["name"] : $fromName;
            $this->email->from(new Address($fromAddress, $fromName));
        }

        // TO:
        if( !empty($toAddress) && !empty($toName) ) {

            $this->email->to(new Address($toAddress, $toName));

        } else if( !empty($toAddress) ) {

            $this->email->to(new Address($toAddress));
        }

        // CC:
        if( !empty($arrCc) ) {

            foreach($arrCc as $ccAddress) {

                $this->email->addCc(new Address($ccAddress));
            }
        }

        // SUBJECT:
        $subjectTag     = empty($this->arrMailerConfig["subject"]["tag"]) ? null : trim($this->arrMailerConfig["subject"]["tag"]);
        $subjectTagged  = empty($subjectTag) ? $subjectUntagged : ($subjectTag . " " . $subjectUntagged);
        $subject        = $addTagToSubject ? $subjectTagged : $subjectUntagged;

        // BUILDING
        $this->email
            ->subject($subject)
            ->htmlTemplate($templateFilename)
            ->context(array_merge($arrTemplateData, [ "emailEnvelopeData" => [
                "toAddress"     => $toAddress,
                "toName"        => $toName,
                "subject"       => [
                        "tag"       => $subjectTag,
                        "untagged"  => $subjectUntagged,
                        "used"      => $subject
                ],
                "fromAddress"   => $fromAddress,
                "fromName"      => $fromName
            ]]));

        return $this;
    }


    public function addUnsubscribeLink(string $url, string $emailAddress): static
    {
        // https://datatracker.ietf.org/doc/html/rfc2369#section-3.2
        $this->email->getHeaders()->addTextHeader('List-Unsubscribe',
            '<' . $url . '>, <mailto:' . $emailAddress . '>'
        );

        return $this;
    }


    public function send()
    {
        return $this->mailer->send($this->email);
    }
}
