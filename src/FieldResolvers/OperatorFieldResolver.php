<?php
namespace PoP\Engine\FieldResolvers;

use PoP\Engine\Misc\Extract;
use PoP\ComponentModel\Engine_Vars;
use PoP\FieldQuery\FieldQueryUtils;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\FieldResolvers\AbstractOperatorOrHelperFieldResolver;

class OperatorFieldResolver extends AbstractOperatorOrHelperFieldResolver
{
    public const HOOK_SAFEVARS = __CLASS__.':safeVars';
    public static function getFieldNamesToResolve(): array
    {
        return [
            'if',
            'not',
            'and',
            'or',
            'equals',
            'empty',
            'isNull',
            'var',
            'context',
            'sprintf',
            'concat',
            'echo',
            'divide',
            'time',
            'arrayRandom',
            'arrayJoin',
            'arrayItem',
            'arraySearch',
            'arrayFill',
            'arrayValues',
            'arrayUnique',
            'arrayDiff',
            'arrayAddItem',
            'arrayAsQueryStr',
            'extract',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'if' => SchemaDefinition::TYPE_MIXED,
            'not' => SchemaDefinition::TYPE_BOOL,
            'and' => SchemaDefinition::TYPE_BOOL,
            'or' => SchemaDefinition::TYPE_BOOL,
            'equals' => SchemaDefinition::TYPE_BOOL,
            'empty' => SchemaDefinition::TYPE_BOOL,
            'isNull' => SchemaDefinition::TYPE_BOOL,
            'var' => SchemaDefinition::TYPE_MIXED,
            'context' => SchemaDefinition::TYPE_OBJECT,
            'sprintf' => SchemaDefinition::TYPE_STRING,
            'concat' => SchemaDefinition::TYPE_STRING,
            'echo' => SchemaDefinition::TYPE_MIXED,
            'divide' => SchemaDefinition::TYPE_FLOAT,
            'time' => SchemaDefinition::TYPE_INT,
            'arrayRandom' => SchemaDefinition::TYPE_MIXED,
            'arrayJoin' => SchemaDefinition::TYPE_STRING,
            'arrayItem' => SchemaDefinition::TYPE_MIXED,
            'arraySearch' => SchemaDefinition::TYPE_MIXED,
            'arrayFill' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
            'arrayValues' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
            'arrayUnique' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
            'arrayDiff' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
            'arrayAddItem' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
            'arrayAsQueryStr' => SchemaDefinition::TYPE_STRING,
            'extract' => SchemaDefinition::TYPE_MIXED,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'if' => $translationAPI->__('If a boolean property is true, execute a field, else, execute another field', 'component-model'),
            'not' => $translationAPI->__('Return the opposite value of a boolean property', 'component-model'),
            'and' => $translationAPI->__('Return an `AND` operation among several boolean properties', 'component-model'),
            'or' => $translationAPI->__('Return an `OR` operation among several boolean properties', 'component-model'),
            'equals' => $translationAPI->__('Indicate if the result from a field equals a certain value', 'component-model'),
            'empty' => $translationAPI->__('Indicate if a value is empty', 'component-model'),
            'isNull' => $translationAPI->__('Indicate if a value is null', 'component-model'),
            'var' => $translationAPI->__('Retrieve the value of a certain property from the `$vars` context object', 'component-model'),
            'context' => $translationAPI->__('Retrieve the `$vars` context object', 'component-model'),
            'sprintf' => $translationAPI->__('Replace placeholders inside a string with provided values', 'component-model'),
            'concat' => $translationAPI->__('Concatenate two or more strings', 'component-model'),
            'echo' => $translationAPI->__('Repeat back the input, whatever it is', 'component-model'),
            'divide' => $translationAPI->__('Divide a number by another number', 'component-model'),
            'time' => $translationAPI->__('Return the time now (https://php.net/manual/en/function.time.php)', 'component-model'),
            'arrayRandom' => $translationAPI->__('Randomly select one element from the provided ones', 'component-model'),
            'arrayJoin' => $translationAPI->__('Join all the strings in an array, using a provided separator', 'component-model'),
            'arrayItem' => $translationAPI->__('Access the element on the given position in the array', 'component-model'),
            'arraySearch' => $translationAPI->__('Search in what position is an element placed in the array. If found, it returns its position (integer), otherwise it returns `false` (boolean)', 'component-model'),
            'arrayFill' => $translationAPI->__('Fill a target array with elements from a source array, where a certain property is the same', 'component-model'),
            'arrayValues' => $translationAPI->__('Return the values from a two-dimensional array', 'component-model'),
            'arrayUnique' => $translationAPI->__('Filters out all duplicated elements in the array', 'component-model'),
            'arrayDiff' => $translationAPI->__('Return an array containing all the elements from the first array which are not present on any of the other arrays', 'component-model'),
            'arrayAddItem' => $translationAPI->__('Adds an element to the array', 'component-model'),
            'arrayAsQueryStr' => $translationAPI->__('Represent an array as a string', 'component-model'),
            'extract' => $translationAPI->__('Given an object, it retrieves the data under a certain path', 'pop-component-model'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'if':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'condition',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_BOOL,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The condition to check if its value is `true` or `false`', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'then',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to return if the condition evals to `true`', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'else',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to return if the condition evals to `false`', 'component-model'),
                    ],
                ];

            case 'not':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_BOOL,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value from which to return its opposite value', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'and':
            case 'or':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'values',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_BOOL),
                        SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                            $translationAPI->__('The array of values on which to execute the `%s` operation', 'component-model'),
                            strtoupper($fieldName)
                        ),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'equals':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value1',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The first value to compare', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value2',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The second value to compare', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'empty':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to check if it is empty', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'isNull':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to check if it is null', 'component-model'),
                    ],
                ];

            case 'var':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'name',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The name of the variable to retrieve from the `$vars` context object', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'sprintf':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'string',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The string containing the placeholders', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'values',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The values to replace the placeholders with inside the string', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'concat':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'values',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Strings to concatenate', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'echo':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The input to be echoed back', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'divide':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'number',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_FLOAT,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Number to divide', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'by',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_FLOAT,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The division operandum', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'arrayRandom':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Array of elements from which to randomly select one', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ]
                ];

            case 'arrayJoin':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Array of strings to be joined all together', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'separator',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Separator with which to join all strings in the array', 'component-model'),
                    ],
                ];

            case 'arrayItem':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Array containing the element to retrieve', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'position',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Position where the element is placed in the array, starting from 0', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'arraySearch':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Array containing the element to search', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'element',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Element to search in the array and retrieve its position', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'arrayFill':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'target',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Array to be added elements coming from the source array', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'source',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Array whose elements will be added to the target array', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'index',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Property whose value must be the same on both arrays', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'properties',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Properties to copy from the source to the target array. If empty, all properties in the source array will be copied', 'component-model'),
                    ],
                ];

            case 'arrayValues':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The array from which to retrieve the values', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'arrayUnique':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The array to operate on', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'arrayDiff':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'arrays',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The array containing all the arrays. It must have at least 2 elements', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'arrayAddItem':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The array to add an item on', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to add to the array', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'key',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Key (string or integer) under which to add the value to the array. If not provided, the value is added without key', 'component-model'),
                    ],
                ];

            case 'arrayAsQueryStr':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'array',
                        SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_MIXED),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The array to represented as a string', 'component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];
            case 'extract':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'object',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_OBJECT,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The object to retrieve the data from', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'path',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The path to retrieve data from the object. Paths are separated with \'.\' for each sublevel', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];
        }

        return parent::getSchemaFieldArgs($typeResolver, $fieldName);
    }

    public function resolveSchemaValidationErrorDescription(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        if ($error = parent::resolveSchemaValidationErrorDescription($typeResolver, $fieldName, $fieldArgs)) {
            return $error;
        }

        // Important: The validations below can only be done if no fieldArg contains a field!
        // That is because this is a schema error, so we still don't have the $resultItem against which to resolve the field
        // For instance, this doesn't work: /?query=arrayItem(posts(),3)
        // In that case, the validation will be done inside ->resolveValue(), and will be treated as a $dbError, not a $schemaError
        if (!FieldQueryUtils::isAnyFieldArgumentValueAField($fieldArgs)) {
            $translationAPI = TranslationAPIFacade::getInstance();
            switch ($fieldName) {
                case 'var':
                    $safeVars = $this->getSafeVars();
                    if (!isset($safeVars[$fieldArgs['name']])) {
                        return sprintf(
                            $translationAPI->__('Var \'%s\' does not exist in `$vars`', 'component-model'),
                            $fieldArgs['name']
                        );
                    };
                    return null;
                case 'arrayItem':
                    if (count($fieldArgs['array']) < $fieldArgs['position']) {
                        return sprintf(
                            $translationAPI->__('The array contains no element at position \'%s\'', 'component-model'),
                            $fieldArgs['position']
                        );
                    };
                    return null;
                case 'arrayDiff':
                    if (count($fieldArgs['arrays']) < 2) {
                        return sprintf(
                            $translationAPI->__('The array must contain at least 2 elements: \'%s\'', 'component-model'),
                            json_encode($fieldArgs['arrays'])
                        );
                    };
                case 'divide':
                    if ($fieldArgs['by'] === (float)0) {
                        return $translationAPI->__('Cannot divide by 0', 'component-model');
                    }
                    // Check that all items are arrays
                    // This doesn't work before resolving the args! So doing arrayDiff([echo($langs),[en]]) fails
                    // $allArrays = array_reduce($fieldArgs['arrays'], function($carry, $item) {
                    //     return $carry && is_array($item);
                    // }, true);
                    // if (!$allArrays) {
                    //     return sprintf(
                    //         $translationAPI->__('The array must contain only arrays as elements: \'%s\'', 'component-model'),
                    //         json_encode($fieldArgs['arrays'])
                    //     );
                    // }
                    return null;
            }
        }

        return null;
    }

    protected function getSafeVars() {
        if (is_null($this->safeVars)) {
            $this->safeVars = Engine_Vars::getVars();
            HooksAPIFacade::getInstance()->doAction(
                self::HOOK_SAFEVARS,
                array(&$this->safeVars)
            );
        }
        return $this->safeVars;
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        switch ($fieldName) {
            case 'if':
                if ($fieldArgs['condition']) {
                    return $fieldArgs['then'];
                } elseif (isset($fieldArgs['else'])) {
                    return $fieldArgs['else'];
                }
                return null;
            case 'not':
                return !$fieldArgs['value'];
            case 'and':
                return array_reduce($fieldArgs['values'], function($accumulated, $value) {
                    $accumulated = $accumulated && $value;
                    return $accumulated;
                }, true);
            case 'or':
                return array_reduce($fieldArgs['values'], function($accumulated, $value) {
                    $accumulated = $accumulated || $value;
                    return $accumulated;
                }, false);
            case 'equals':
                return $fieldArgs['value1'] == $fieldArgs['value2'];
            case 'empty':
                return empty($fieldArgs['value']);
            case 'isNull':
                return is_null($fieldArgs['value']);
            case 'var':
                $safeVars = $this->getSafeVars();
                return $safeVars[$fieldArgs['name']];
            case 'context':
                return $this->getSafeVars();
            case 'sprintf':
                return sprintf($fieldArgs['string'], ...$fieldArgs['values']);
            case 'concat':
                return array_reduce(
                    $fieldArgs['values'],
                    function($carry, $item) {
                        return $carry.$item;
                    },
                    ''
                );
            case 'echo':
                return $fieldArgs['value'];
            case 'divide':
                return (float)$fieldArgs['number']/(float)$fieldArgs['by'];
            case 'time':
                return time();
            case 'arrayRandom':
                return $fieldArgs['array'][array_rand($fieldArgs['array'])];
            case 'arrayJoin':
                return implode($fieldArgs['separator'] ?? '', $fieldArgs['array']);
            case 'arrayItem':
                return $fieldArgs['array'][$fieldArgs['position']];
            case 'arraySearch':
                return array_search($fieldArgs['element'], $fieldArgs['array']);
            case 'arrayFill':
                // For each element in the source, iterate all the elements in the target
                // If the value for the index property is the same, then copy the properties
                $value = $fieldArgs['target'];
                $index = $fieldArgs['index'];
                foreach ($value as &$targetProps) {
                    foreach ($fieldArgs['source'] as $sourceProps) {
                        if (array_key_exists($index, $targetProps) && $targetProps[$index] == $sourceProps[$index]) {
                            $properties = $fieldArgs['properties'] ? $fieldArgs['properties'] : array_keys($sourceProps);
                            foreach ($properties as $property) {
                                $targetProps[$property] = $sourceProps[$property];
                            }
                        }
                    }
                }
                return $value;
            case 'arrayValues':
                return array_values($fieldArgs['array']);
            case 'arrayUnique':
                return array_unique($fieldArgs['array']);
            case 'arrayDiff':
                // Diff the first array against all the others
                $arrays = $fieldArgs['arrays'];
                $first = (array)array_shift($arrays);
                return array_diff($first, ...$arrays);
            case 'arrayAddItem':
                $array = $fieldArgs['array'];
                if ($fieldArgs['key']) {
                    $array[$fieldArgs['key']] = $fieldArgs['value'];
                } else {
                    $array[] = $fieldArgs['value'];
                }
                return $array;
            case 'arrayAsQueryStr':
                $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
                return $fieldQueryInterpreter->getArrayAsStringForQuery($fieldArgs['array']);
            case 'extract':
                return Extract::getDataFromPath($fieldName, $fieldArgs['object'], $fieldArgs['path']);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
