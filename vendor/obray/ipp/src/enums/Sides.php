<?php
namespace obray\ipp\enums;

class Sides extends \obray\ipp\types\Enum
{
    const one_sided = 'one-sided';
    const two_sided_long_edge = 'two-sided-long-edge';
    const two_sided_short_edge = 'two-sided-short-edge';
    const tumble = 'tumble';
}