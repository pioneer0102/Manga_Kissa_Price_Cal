# Makefile for PHP project linting and quality checks

.PHONY: help install lint lint-fix phpcs phpcs-fix phpstan test clean

# Default target
help: ## Show this help message
	@echo Usage: make [target]
	@echo.
	@echo Targets:
	@echo   help           Show this help message
	@echo   install        Install composer dependencies
	@echo   lint           Run PHP CS Fixer (dry run)
	@echo   lint-fix       Fix code style with PHP CS Fixer
	@echo   phpcs          Run PHP CodeSniffer
	@echo   phpcs-fix      Fix code style with PHP CodeSniffer
	@echo   phpstan        Run PHPStan static analysis
	@echo   syntax-check   Check PHP syntax
	@echo   lint-all       Run all linting tools
	@echo   fix-all        Fix all code style issues and run complete quality check
	@echo   dev            Run the application
	@echo   test           Run tests
	@echo   full-check     Run complete quality check
	@echo   clean          Clean cache and temporary files

install: ## Install composer dependencies
	composer install

lint: ## Run PHP CS Fixer (dry run)
	composer run lint

lint-fix: ## Fix code style with PHP CS Fixer
	composer run lint-fix

phpcs: ## Run PHP CodeSniffer
	composer run phpcs

phpcs-fix: ## Fix code style with PHP CodeSniffer
	composer run phpcs-fix

phpstan: ## Run PHPStan static analysis
	composer run phpstan

syntax-check: ## Check PHP syntax
	composer run syntax-check

lint-all: ## Run all linting tools
	composer run lint-all

fix-all: ## Fix all code style issues and run complete quality check
	composer run fix-all

dev: ## Run the application
	composer run dev

test: ## Run tests
	composer run test

full-check: ## Run complete quality check
	composer run syntax-check lint-all test 

clean: ## Clean cache and temporary files
	@if exist vendor rmdir /s /q vendor
	@if exist .php-cs-fixer.cache del .php-cs-fixer.cache
