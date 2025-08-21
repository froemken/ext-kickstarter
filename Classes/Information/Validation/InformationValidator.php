<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InformationValidator
{
    public function validate(InformationInterface $information): void
    {
        $rc = new \ReflectionClass($information);
        $errors = [];

        foreach ($rc->getProperties() as $prop) {
            $attr = $prop->getAttributes(UseValidator::class)[0] ?? null;
            if (!$attr) {
                continue;
            }

            /** @var UseValidator $meta */
            $meta = $attr->newInstance();
            $validator = $this->getValidator($meta);

            $value = $this->readValue($information, $prop->getName());

            try {
                // ValidatorInterface is invokable
                $validator($value, $information, ['fieldName' => $prop->getName()]);
            } catch (\RuntimeException $e) {
                $errors[$prop->getName()] = $e->getMessage();
            }
        }

        if ($errors !== []) {
            throw new InformationValidationException($errors, 5710226770);
        }
    }

    private function readValue(object $object, string $propName): mixed
    {
        // Prefer a getter if it exists
        $getter = 'get' . ucfirst($propName);
        if (method_exists($object, $getter)) {
            return $object->$getter();
        }
        // Use a isser if it exists
        $getter = 'is' . ucfirst($propName);
        if (method_exists($object, $getter)) {
            return $object->$getter();
        }
        // Use a hasser if it exists
        $getter = 'has' . ucfirst($propName);
        if (method_exists($object, $getter)) {
            return $object->$getter();
        }
        throw new \BadMethodCallException(sprintf('Method %s does not exist', $getter), 6315933720);
    }

    public function getValidator(UseValidator $meta): ValidatorInterface
    {
        return GeneralUtility::makeInstance($meta->serviceId);
    }
}
