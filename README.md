# Associations fieldtypes for Craft CMS

Fieldtypes which allow to assocate extra data with 
- field
- dropdown/checkbox options

It mimics the Table fieldtype (but with predefined columns). Returns data as table array, which you can loop through like this (assuming avPrices is handle of global and prices is handle of field) :
``{% set prices = {} %}
{% for price in avPrices.prices %}
    {% set prices = prices|merge({(price.orig): price.assoc}) %}
{% endfor %}``