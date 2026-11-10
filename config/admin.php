<?php

return [
  "accounts" => [
    [
      "name" => env("ADMIN_1_NAME", "Admin"),
      "email" => env("ADMIN_1_EMAIL"),
      "password" => env("ADMIN_1_PASSWORD"),
    ],
    [
      "name" => env("ADMIN_2_NAME", "Admin"),
      "email" => env("ADMIN_2_EMAIL"),
      "password" => env("ADMIN_2_PASSWORD"),
    ],
  ],
];
