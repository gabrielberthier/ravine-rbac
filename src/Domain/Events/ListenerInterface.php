<?php
namespace RavineRbac\Domain\Events;

/**
 * @template T Event
 */
interface ListenerInterface
{
    /**
     * @param T $subject
     */
    public function execute(Event $subject): void;
}