Access Control
==============

Goal: describe which actions and routes should have restricted access and under which conditions users can access them.

**Types of users**

- Unauthenticated guest
- Authenticated free tier user
- Authenticated full-access user
- Administrator? Needed?

I'd manage this with mutually exclusive ``is_free_tier`` and ``is_full_tier`` columns in the ``users`` table.

Unauthenticated guest
---------------------

**Allowed**: 

- Ingredient Index (only show Ingredients with null ``user_id``)
- Ingredients Show for Ingredients with null ``user_id``
- RDI Profile Index (only show RDI Profiles with null ``user_id``)
- RDI Profile Show for RDI Profiles with null ``user_id``

**Restricted**:

- Ingredient Create, Update, Delete; and Ingredient Show for Ingredients with a non-null ``user_id``
- Meal CRUD
- Food List CRUD
- RDI Profiles Create, Update, Delete; and RDI Profiles Show for RDI Profiles with a non-null ``user_id``

Authenticated free tier
-----------------------

**Allowed**:

- Ingredient Index, Show, Delete, Update where ``ingredients.user_id`` matches ``users.id``
- Ingredient Create for up to N models
- Meal Index, Show, Delete, Update where ``meals.user_id`` matches ``users.id``
- Meal Create for up to N models
- Food List Index, Show, Delete, Update where ``food_lists.user_id`` matches ``users.id``
- Food Lists Create for up to N models
- RDI Profile Index (only show RDI Profiles with null ``user_id``)
- RDI Profile Show for RDI Profiles with null ``user_id``

**Restricted**:

- Ingredient Create over N models
- Ingredient Show, Delete, Update where ``ingredients.user_id`` does not match ``users.id``
- Meal Create over over N models
- Meal Show, Delete, Update where ``meals.user_id`` does not match ``users.id``
- Food List Create over N models
- Food List Show, Delete, Update where ``food_lists.user_id`` does not match ``users.id``
- RDI Profiles Create, Update, Delete; and RDI Profiles Show for RDI Profiles with a non-null ``user_id``

Authenticated full access
-------------------------

**Allowed**:

- Ingredient CRUD where ``ingredients.user_id`` matches ``users.id``
- Meal CRUD where ``meals.user_id`` matches ``users.id``
- Food List CRUD where ``food_lists.user_id`` matches ``users.id``
- RDI Profile CRUD where ``rdi_profiles.user_id`` matches ``users.id``

**Restricted**:

- Ingredient Show, Delete, Update where ``ingredients.user_id`` does not match ``users.id``
- Meal Show, Delete, Update where ``meals.user_id`` does not match ``users.id``
- Food List Show, Delete, Update where ``food_lists.user_id`` does not match ``users.id``
- RDI Profile Show, Delete, Update where ``rdi_profiles.user_id`` does not match ``users.id``
