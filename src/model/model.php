<?php
interface Model {
  // An implementing class MUST have a publicly-readable property,
  // but whether or not it's publicly settable is unrestricted.
  public string $readable { get; }

  // An implementing class MUST have a publicly-writeable property,
  // but whether or not it's publicly readable is unrestricted.
  public string $writeable { set; }

  // An implementing class MUST have a property that is both publicly
  // readable and publicly writeable.
  public string $both { get; set; }
}

