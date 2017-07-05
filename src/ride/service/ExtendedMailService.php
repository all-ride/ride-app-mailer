<?php

namespace ride\service;

use ride\library\config\Config;
use ride\library\mail\exception\TemplateNotFoundMailException;
use ride\library\mail\handler\MailHandler;
use ride\library\mail\template\MailTemplateProvider;
use ride\library\mail\template\MailTemplate;
use ride\library\mail\type\MailTypeProvider;
use ride\library\mail\type\MailType;

/**
 * Base mail service with mail template storage for global mail types
 */
class ExtendedMailService extends MailService {

    /**
     * Prefix for the mail type parameter to store the templates for a global
     * application mail type.
     * @var string
     */
    const PARAM_MAIL_TYPE = 'mail.type';

    /**
     * Instance of the configuration
     * @var \ride\library\config\Config
     */
    protected $config;

    /**
     * Constructs the mail service
     * @param \ride\library\mail\type\MailTypeProvider $mailTypeProvider
     * @param \ride\library\mail\template\MailTemplateProvider $mailTemplateProvider
     * @param \ride\library\mail\handler\MailHandler $mailHandler
     * @param \ride\library\config\Config $config
     * @return null
     */
    public function __construct(MailTypeProvider $mailTypeProvider, MailTemplateProvider $mailTemplateProvider, MailHandler $mailHandler, Config $config) {
        parent::__construct($mailTypeProvider, $mailTemplateProvider, $mailHandler);

        $this->config = $config;
    }

    /**
     * Gets the global mail templates for the provided mail type
     * @param string $mailType Machine name of the mail type, result of getName
     * @param string $locale Code of the locale
     * @return array Array with the set mail templates
     * @see \ride\library\mail\template\MailTemplate
     */
    public function getMailTemplatesForType($mailType, $locale) {
        $mailTemplates = array();

        $key = $this->getParameterKeyForType($mailType);

        $mailTemplateIds = $this->config->get($key);
        if (!$mailTemplateIds) {
            return $mailTemplates;
        }

        foreach ($mailTemplateIds as $mailTemplateId) {
            try {
                $mailTemplate = $this->mailTemplateProvider->getMailTemplate($mailTemplateId, $locale);
                $mailTemplates[$mailTemplateId] = $mailTemplate;
            } catch (TemplateNotFoundMailException $exception) {
                // just ignore unexistant templates
            }
        }

        return $mailTemplates;
    }

    /**
     * Sets the global mail templates for the provided mail type
     * @param string|\ride\library\mail\type\MailType $mailType Name or instance
     * of the mail type
     * @param array $mailTemplates Instances of the mail templates to use for
     * the provided mail type
     * @return null
     */
    public function setMailTemplatesForType($mailType, array $mailTemplates) {
        $mailTemplateIds = array();

        foreach ($mailTemplates as $index => $mailTemplate) {
            if (!$mailTemplate instanceof MailTemplate) {
                throw new MailException('Could not set mail template: value on index ' . $index . ' is not an instance of ride\\library\\mail\\template\\MailTemplate');
            }

            $mailTemplateIds[$mailTemplate->getId()] = $mailTemplate->getId();
        }

        $key = $this->getParameterKeyForType($mailType);

        $this->config->set($key, $mailTemplateIds);
    }

    /**
     * Gets the parameter key for the provided mail type
     * @param string|\ride\library\mail\type\MailType $mailType Name or instance
     * of the mail type
     * @return string
     */
    protected function getParameterKeyForType($mailType) {
        if ($mailType instanceof MailType) {
            $mailType = $mailType->getName();
        }

        return self::PARAM_MAIL_TYPE . '.' . $mailType;
    }

}
