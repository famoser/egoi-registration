# Introduction
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![Build Status](https://travis-ci.com/famoser/egoi-registration.svg?branch=main)](https://travis-ci.com/famoser/egoi-registration)
[![Scrutinizer](https://scrutinizer-ci.com/g/famoser/egoi-registration/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/famoser/egoi-registration/)

## About
The European Girls' Olympiad in Informatics (EGOI) is a new programming competition just for girls. Inspired by the success of the European Girls' Mathematical Olympiad we want to create a similar competition in Informatics. The format of the competition will be similar to established competitions such as the International Olympiad in Informatics or the Central European Olympiad in Informatics.

This tool will allow the participants to register. It includes experiences of previous executions of such olympiads.

It suggests the following workflow:
- the organizer defines which countries participate and generates an authentication link for each country
- the team leader of each country signs up using that authentication link
- the team leader defines how many atheletes, leaders & guests will join, and if they will join locally
- the team leader records data of each attending person: 
    - personal data (name, age, email, phone)
    - immigration (passport & nationality infos)
    - event specific details (badge name, portrait, diet, allergies, shirt size, leaders define translations they will do)
    - arrival information (location, means of travel, details of means of travel)
    - parental consent download & upload
- the organizer reviews the information, and if complete, locks it

Further, the organizer has the following capabilities:
- impersonate a team leader (see & use the UI as the team leader would do)
- overview of new & reviewed information of the team leaders
- export as csv
