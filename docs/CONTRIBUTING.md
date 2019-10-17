# Contributing

Thank you for thinking about contributing to the Core Sitemaps project! Contributions can either be made in the form of code or through input on tickets.
We appreciate all contributions as they help to move the project forward.


## Contributions to Issues

Tickets will often need input from multiple points of view. We invite you to share your POV by adding a comment to the relevant ticket.

If a ticket does not yet exist, you can create a new ticket and add it to the back log, but please do read: https://make.wordpress.org/core/2019/06/12/xml-sitemaps-feature-project-proposal/ and double check that what you are proposing is in line with what we are trying to achieve within this project's scope.


### Creating an Issue

- In Github, under 'Issues' click the green 'new issue' button.
- Give the issue a short but meaningful title. This should be descriptive, summarize the issue in 5-10 words at most.
- Give us much detail about the issue as possible in the description box.

## Developer Contributions

When contributing through code, each feature should be developed in a seperate branch.

- Create a new branch, forked from `develop`.
- Each branch should be prefixed with `feature/` and the issue number, followed by a short-description of the task. For example: Issue 3: Index Sitemap would become `feature/3-index-sitemap`.
- Regularly commit your work and push it to your remote branch on Github.


### Submitting Pull Requests ###

Once you are ready to submit your code for review, you need to create a pull request (PR).

- Under 'Pull Requests', click the green 'New pull request' button.
- When comparing changes, ensure that the base is set to `develop` and compare is set to your feature branch.
- Give the PR a title. This should be descriptive, summarize what your request is about in 5-10 words at most.
- Add a description, outlining what you have done, and what problem this solves. Include screenshots if possible to help visualize what changes have been introduced, and reference any open
issues if one exists.

When submitting a PR there are some items you should take note of:
- Does the code follow the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/)?
- Did you include unit tests (if applicable)?
- Was your local copy recently pulled from `develop`, so it's a clean patch?


## Contribute to the Documentation

All of the documentation for this plugin lives at [/docs/](/docs/README.md)

To contribute to the documentation either follow the Contributions to Issues workflow above and leave a comment about your suggested change. Or, follow the Developer Contributions workflow, and create a pull request with your suggested changes in.

## Reporting Security Issues

If you find a security issue, please do not post about it publicly anywhere. Even if there’s a report filed on one of the official security tracking sites, bringing more awareness to the security issue tends to increase people being hacked, and rarely speeds up the fixing.

Please email plugins@wordpress.org with a clear and concise description of the issue. It greatly helps if you can provide us with how you verified this is an exploit (links to the plugin listing on sites like secunia.com are perfect).
