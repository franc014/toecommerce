# Porting current ecommerce Filament dashboard implementations to a new Filament plugin  

Create a plan to port the current Filament Dashboard which is the backstore of the ecommerce, to a new package called `phi-ecommerce-core` . The Filament plugin skeleton can be found locally at `/Users/franciscoandrade/Herd/phi-ecommerce-core`

## What should be ported into core package

- Filament resources
- Enums used throughout the Filament dashboard: forms, tables, store front settings, etc
- Models
- Casts

And anything related to the core backstore business logic of the ecommerce

## What should not be ported into core package

- Storefront buisness logic related to ecommerce:
	- Cart functionality
	- Product listings
	- Checkout
- CMS logic: Pages, Sections, Company settings information, Content Builders, Forms related to CMS logic, etc.
- Authentication / Authorization logic. We use Filament Shield plugin that wraps Spatie permission package. We don't need this ported to the core yet. Maybe later we'll do it

The storefront logic development will be driven later from scratch, so we don't need this logic inside the core package.

Let's first draft a plan to accomplish this. Ask me anything through the question tool, to dig deeper or know more about this task.