<?php

namespace ride\application\mail\type;

use ride\library\dependency\exception\DependencyNotFoundException;
use ride\library\dependency\DependencyInjector;
use ride\library\mail\exception\TypeNotFoundMailException;
use ride\library\mail\type\MailTypeProvider;

/**
 * Provider of the mail types through the dependency injector
 */
class DependencyMailTypeProvider implements MailTypeProvider {

    /**
     * Instance of the dependency injector
     * @var \ride\library\dependency\DependencyInjector
     */
    private $dependencyInjector;

    /**
     * Constructs a new dependency mail type provider
     * @param \ride\library\dependency\DependencyInjector $dependencyInjector
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Gets the available mail types
     * @return array Array with the machine name of the mail type as key and an
     * instance of the mail type as value
     */
    public function getMailTypes() {
        $result = array();

        $mailTypes = $this->dependencyInjector->getAll('ride\\library\\mail\\type\\MailType');
        foreach ($mailTypes as $mailType) {
            $result[$mailType->getName()] = $mailType;
        }

        return $result;
    }

    /**
     * Gets the available mail types which are marked as global
     * @return array Array with the machine name of the mail type as key and an
     * instance of the mail type as value
     */
    public function getGlobalMailTypes() {
        $result = array();

        $mailTypes = $this->dependencyInjector->getByTag('ride\\library\\mail\\type\\MailType', 'global');
        foreach ($mailTypes as $mailType) {
            $result[$mailType->getName()] = $mailType;
        }

        return $result;
    }

    /**
     * Gets a specific mail type
     * @param string $name Machine name of the mail type
     * @return MailType Instance of the mail type
     * @throws TypeNotFoundMailException when the mail type does not exist
     */
    public function getMailType($name) {
        if ($name === null) {
            throw new TypeNotFoundMailException('Could not find mail type since no name is provided');
        }

        try {
            $mailType = $this->dependencyInjector->get('ride\\library\\mail\\type\\MailType', $name);
        } catch (DependencyNotFoundException $exception) {
            $mailTypes = $this->getMailTypes();
            if (isset($mailTypes[$name])) {
                $mailType = $mailTypes[$name];
            } else {
                throw new TypeNotFoundMailException('Could not find mail type with name ' . $name, 0, $exception);
            }
        }

        return $mailType;
    }

}
