<?php

namespace Mine\Kernel\GeneratorCrud\Enums;

enum TableColumnType: string
{
    case STRING = 'string';

    case TEXT = 'text';

    case INTEGER = 'integer';

    case BIGINT = 'bigint';

    case FLOAT = 'float';

    case DOUBLE = 'double';

    case DECIMAL = 'decimal';

    case BOOLEAN = 'boolean';

    case DATE = 'date';

    case DATETIME = 'datetime';

    case TIMESTAMP = 'timestamp';

    case TIME = 'time';
}
