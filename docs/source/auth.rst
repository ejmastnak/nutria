Authorization
=============

Goal: describe which actions and routes should have restricted access and under which conditions users can access them.

Roles and authorization Errors
------------------------------

Types of users
^^^^^^^^^^^^^^

- Unauthenticated guest
- Authenticated free tier user
- Authenticated full-access user
- Administrator

I'd manage this with mutually exclusive ``is_free_tier``, ``is_full_tier``, and ``is_admin`` columns in the ``users`` table.

Types of authorization errors
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

- ``requires_account`` shown to guest users who must create an account to access a feature.
- ``free_tier_exceeded`` shown to free tier users who must upgrade to a full-access account to create more resources.
- ``does_not_own`` rendered as a 404 Not Found to users accessing resources they do not own
- ``requires_full_access`` shown to guest or free tier users who must create or upgrade to a full-access account to access a feature.

Unauthenticated guest
---------------------

**Allowed**: 

- Ingredient Index (only show Ingredients with null ``user_id``)
- Ingredients Show for Ingredients with null ``user_id``
- Intake Guideline Index (only show Intake Guidelines with null ``user_id``)
- Intake Guideline Show for Intake Guidelines with null ``user_id``

**Restricted**:

- Ingredient Create, Update, Delete; and Ingredient Show for Ingredients with a non-null ``user_id``
- Meal CRUD
- Food List CRUD
- Intake Guidelines Create, Update, Delete; and Intake Guidelines Show for Intake Guidelines with a non-null ``user_id``

Authenticated free tier
-----------------------

**Allowed**:

- Ingredient Index, Show, Delete, Update where ``ingredients.user_id`` matches ``users.id``
- Ingredient Create for up to N models
- Meal Index, Show, Delete, Update where ``meals.user_id`` matches ``users.id``
- Meal Create for up to N models
- Food List Index, Show, Delete, Update where ``food_lists.user_id`` matches ``users.id``
- Food Lists Create for up to N models
- Intake Guideline Index (only show Intake Guidelines with null ``user_id``)
- Intake Guideline Show for Intake Guidelines with null ``user_id``

**Restricted**:

- Ingredient Create over N models
- Ingredient Show, Delete, Update where ``ingredients.user_id`` does not match ``users.id``
- Meal Create over over N models
- Meal Show, Delete, Update where ``meals.user_id`` does not match ``users.id``
- Food List Create over N models
- Food List Show, Delete, Update where ``food_lists.user_id`` does not match ``users.id``
- Intake Guidelines Create, Update, Delete; and Intake Guidelines Show for Intake Guidelines with a non-null ``user_id``

Authenticated full access
-------------------------

**Allowed**:

- Ingredient CRUD where ``ingredients.user_id`` matches ``users.id``
- Meal CRUD where ``meals.user_id`` matches ``users.id``
- Food List CRUD where ``food_lists.user_id`` matches ``users.id``
- Intake Guideline CRUD where ``intake_guidelines.user_id`` matches ``users.id``

**Restricted**:

- Ingredient Show, Delete, Update where ``ingredients.user_id`` does not match ``users.id``
- Meal Show, Delete, Update where ``meals.user_id`` does not match ``users.id``
- Food List Show, Delete, Update where ``food_lists.user_id`` does not match ``users.id``
- Intake Guideline Show, Delete, Update where ``intake_guidelines.user_id`` does not match ``users.id``

Ingredients
-----------

Index
^^^^^

- Guest: always allowed; displays built-in ingredients
- Free: always allowed; displays built-in and owned ingredients
- Full: always allowed; displays built-in and owned ingredients

Show
^^^^

- Guest: allowed for built-in ingredients.
  Otherwise forbidden with ``does_not_own``.
- Free: allowed for built-in and owned ingredients. 
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for built-in and owned ingredients. 
  Otherwise forbidden with ``does_not_own``.

Create
^^^^^^

- Guest: forbidden with ``requires_account``
- Free: allowed for up to ``auth.max_free_tier_ingredients``.
  Otherwise forbidden with ``free_tier_exceeded``.
- Full: always allowed.

Clone
^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed when cloning built-in or owned ingredients for up to ``auth.max_free_tier_ingredients`` .
  Otherwise forbidden with ``does_not_own`` or ``free_tier_exceeded``.
- Full: allowed when cloning built-in or owned ingredients. 
  Otherwise forbidden with ``does_not_own``.

Update
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned ingredients.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned ingredients.
  Otherwise forbidden with ``does_not_own``.

Delete
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned ingredients.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned ingredients.
  Otherwise forbidden with ``does_not_own``.

Meals
-----

Index
^^^^^

- Guest: forbidden with ``requires_account``.
- Free: always allowed; displays owned meals.
- Full: always allowed; displays owned meals.

Show
^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned meals.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned meals.
  Otherwise forbidden with ``does_not_own``.

Create
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for up to ``auth.max_free_tier_meals``. 
  Otherwise forbidden with ``free_tier_exceeded``.
- Full: always allowed.

Clone
^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed when cloning owned meals for up to ``auth.max_free_tier_meals``.
  Otherwise forbidden with either ``does_not_own`` or ``free_tier_exceeded``.
- Full: allowed when cloning owned meals. 
  Otherwise forbidden with ``does_not_own``.

Update
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned meals.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned meals.
  Otherwise forbidden with ``does_not_own``.

Delete
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned meals.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned meals.
  Otherwise forbidden with ``does_not_own``.

Food Lists
----------

Index
^^^^^

- Guest: forbidden with ``requires_account``.
- Free: always allowed; displays owned food lists.
- Full: always allowed; displays owned food lists.

Show
^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned food lists.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned food lists.
  Otherwise forbidden with ``does_not_own``.

Create
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for up to ``auth.max_free_tier_food_lists``. 
  Otherwise forbidden with ``free_tier_exceeded``.
- Full: always allowed.

Clone
^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed when cloning owned food lists for up to ``auth.max_free_tier_food_lists``.
  Otherwise forbidden with either ``does_not_own`` or ``free_tier_exceeded``.
- Full: allowed when cloning owned food lists. 
  Otherwise forbidden with ``does_not_own``.

Update
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned food lists.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned food lists.
  Otherwise forbidden with ``does_not_own``.

Delete
^^^^^^

- Guest: forbidden with ``requires_account``.
- Free: allowed for owned food lists.
  Otherwise forbidden with ``does_not_own``.
- Full: allowed for owned food lists.
  Otherwise forbidden with ``does_not_own``.

Intake Guidelines
-----------------

Index
^^^^^

- Guest: forbidden with ``requires_full_access``.
- Free: forbidden with ``requires_full_access``.
- Full: always allowed; displays owned food lists.

Show
^^^^

- Guest: forbidden with ``requires_full_access``.
- Free: forbidden with ``requires_full_access``.
- Full: allowed for owned intake guidlines.
  Otherwise forbidden with ``does_not_own``.

Create
^^^^^^

- Guest: forbidden with ``requires_full_access``.
- Free: forbidden with ``requires_full_access``.
- Full: always allowed.

Clone
^^^^^

- Guest: forbidden with ``requires_full_access``.
- Free: forbidden with ``requires_full_access``.
- Full: allowed when cloning owned intake guidelines.
  Otherwise forbidden with ``does_not_own``.

Update
^^^^^^

- Guest: forbidden with ``requires_full_access``.
- Free: forbidden with ``requires_full_access``.
- Full: allowed for owned intake guidelines.
  Otherwise forbidden with ``does_not_own``.

Delete
^^^^^^

- Guest: forbidden with ``requires_full_access``.
- Free: forbidden with ``requires_full_access``.
- Full: allowed for owned intake guidelines.
  Otherwise forbidden with ``does_not_own``.
