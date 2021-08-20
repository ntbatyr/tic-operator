<?php

namespace Cmd;

interface Command
{
    public function run($arguments): bool;
}