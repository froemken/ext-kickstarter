<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validatation;

use ReflectionClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InformationValidator
{
    public function validate(object $information): void
    {
        $rc = new ReflectionClass($information);
        $errors = [];

        foreach ($rc->getProperties() as $prop) {
            $attr = $prop->getAttributes(UseValidator::class)[0] ?? null;
            if (!$attr) {
                continue;
            }

            /** @var UseValidator $meta */
            $meta = $attr->newInstance();
            $validator = GeneralUtility::makeInstance($meta->serviceId);

            $value = $this->readValue($information, $prop->getName());

            try {
                // ValidatorInterface is invokable
                $validator($value);
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

        // Fallback to reflection (works with private props)
        $rp = (new \ReflectionObject($object))->getProperty($propName);
        $rp->setAccessible(true);
        return $rp->getValue($object);
    }
}
