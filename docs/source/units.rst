Units
=====

How to dealing with user input of units.

Problem areas include:

- Ingredient mass input when creating meals (when volume or non-gram units are convenient)

- Internal representation of ingredient amount in meals.
  Use ``mass_in_grams`` or a combination of ``amount`` and ``unit_id``?

- For UI:
  Define and pass to frontend e.g. ``mass_units = [g, oz, kg, etc]`` and ``volume_units = [g, oz, ml, foz, etc]``.
  And then depending on if an ingredient has a non-null ``density`` property, you either allow user to specify amount in a unit from ``mass_units`` or a unit from ``volume_units``.

- Meal amounts in food lists can only be expressed in units of mass.

Have a densitites table with columns ``ingredient_id``, ``density_g_per_ml``.
And a ``ingredient->density`` relationship.
