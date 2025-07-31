<?php

namespace PHPSTORM_META {
    // Allow PhpStorm IDE to resolve return types when calling novelist( Object_Type::class ) or novelist( `Object_Type` ).
    override(
        \Novelist( 0 ),
        map( [
            '' => '@',
            '' => '@Class',
        ] )
    );
}
