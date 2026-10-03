# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.12] - 2026-10-04

### Fixed
- A guest who opens a wishlist link directly, such as /wishlist/index/add/product/9, now gets a clean 404 page. Before, the 404 response still carried a redirect header to the login page and the next page showed the message "You must login or register to add items to your wishlist." even though the wishlist is disabled.
