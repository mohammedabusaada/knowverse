<?php

// Fictional discussion prompts, not research findings or real community activity.
return [
    [
        'author' => 5,
        'title' => 'Authorization belongs at the resource boundary',
        'tags' => ['Web Security', 'Cybersecurity'],
        'body' => "Hiding a button improves the interface, but the server must still decide whether a request is allowed. How do you keep resource ownership, moderator powers, and account status understandable as an application grows?\n\nMy preferred starting point is a policy per resource, supported by tests for the owner, another member, a moderator, and a guest. I would also test direct requests to endpoints whose buttons are hidden.",
        'comments' => [[1, 'The negative cases are especially valuable. I would test a member supplying another user’s resource ID, then assert both the response and the unchanged database state.'], [0, 'Separating participation privileges from moderation roles makes the policy easier to explain. A high reputation score should not automatically grant administrative access.']],
    ],
    [
        'author' => 4,
        'title' => 'What makes a discussion interface comfortable to read?',
        'tags' => ['Psychology', 'Critical Thinking'],
        'body' => "Readable discussion pages need more than a large font. Line length, spacing, contrast, and the placement of voting controls all compete for attention.\n\nI would compare a narrow reading column with a wider layout using the same discussion. Can readers find the main question, distinguish quoted material, and return to a reply without losing their place? Keyboard navigation should be part of the review.",
        'comments' => [[2, 'A useful task is to ask readers to locate a specific argument in a long thread. Completion time alone is not enough; ask where they lost context.'], [5, 'Visible focus states matter here. The reading experience should remain usable when the mouse is unavailable.']],
    ],
    [
        'author' => 3,
        'title' => 'A reproducibility checklist for small machine learning studies',
        'tags' => ['Machine Learning', 'Data Analysis'],
        'body' => "A promising score is difficult to interpret without the experiment around it. For a small study, what is the minimum record that lets another person reproduce the comparison?\n\nI would preserve the dataset version, split procedure, random seeds, preprocessing steps, dependency versions, and evaluation script. Reporting several runs would also make variation visible instead of selecting the best seed.",
        'comments' => [[2, 'Include how missing values were handled. Fitting preprocessing on the full dataset before splitting can leak information into the evaluation.'], [0, 'A small executable example is often more useful than a long setup description. Record the command and expected output alongside the environment.']],
    ],
    [
        'author' => 2,
        'title' => 'Why uncertainty intervals deserve space beside the headline number',
        'tags' => ['Statistics', 'Data Analysis'],
        'body' => "Two experiments can report the same average and still support very different conclusions. Sample size and variability change how much confidence we should place in the comparison.\n\nFor a discussion of a measured effect, I would show the estimate, the uncertainty interval, and the assumptions behind it. Which visual explanations have helped your audience distinguish uncertainty in an estimate from variation among individual observations?",
        'comments' => [[3, 'Showing the individual observations alongside an interval helps avoid treating the average as a complete description of the data.'], [4, 'I would label what the interval represents directly in the chart rather than making readers infer it from the shape.']],
    ],
    [
        'author' => 0,
        'title' => 'Cursor or offset pagination: choosing for the way people browse',
        'tags' => ['Databases', 'Cloud Computing'],
        'body' => "Numbered pages are useful when readers want to jump to a known position. Cursor pagination can be a better fit for a changing activity feed, but it changes the navigation model.\n\nBefore choosing, I would ask whether a stable sort key exists, how often new rows arrive, and whether page numbers are part of the product experience. A benchmark should compare the actual filtered queries, not only an unfiltered table scan.",
        'comments' => [[1, 'A deterministic tie-breaker is essential. Sorting only by a timestamp can leave equally timed records with an unstable order.'], [4, 'The interface also needs a clear way back to the reader’s position. Query efficiency and navigation should be reviewed together.']],
    ],
    [
        'author' => 5,
        'title' => 'Threat modeling an image upload before writing the endpoint',
        'tags' => ['Web Security', 'Cybersecurity'],
        'body' => "An upload feature crosses several boundaries: browser input, server validation, storage, and later delivery to another reader. What would you include in a first threat model?\n\nMy checklist starts with authorization, size limits, content validation, generated filenames, storage location, and safe delivery headers. I would include rejected-upload tests and verify that a failed request leaves no orphaned file.",
        'comments' => [[1, 'The file and its database reference need a cleanup strategy when only one write succeeds. That failure path is easy to miss in a happy-path demo.'], [0, 'I would also test replacing an existing upload. The old file should remain available until the new reference is safely persisted.']],
    ],
    [
        'author' => 3,
        'title' => 'Accuracy is not enough when the rare class matters',
        'tags' => ['Machine Learning', 'Statistics'],
        'body' => "Suppose only one in twenty examples belongs to the class we want to detect. A model that always predicts the majority class can look impressive by accuracy alone.\n\nI would begin with the confusion matrix, then discuss precision and recall in the context of the cost of each mistake. What threshold-selection process would you use when those costs are uncertain?",
        'comments' => [[2, 'Keep the test set separate from threshold selection. Otherwise the reported result partly measures how well we tuned to that particular sample.'], [0, 'For a moderation tool, I would show false positives to reviewers as well as aggregate metrics. The burden on users is part of the evaluation.']],
    ],
    [
        'author' => 2,
        'title' => 'Bayesian updating: making a small example explain itself',
        'tags' => ['Statistics', 'Epistemology'],
        'body' => <<<'MD'
A compact worked example can make the difference between memorizing a formula and understanding what evidence changes.

## From prior belief to a revised estimate

$$P(H \mid E) = \frac{P(E \mid H)P(H)}{P(E)}$$

For a classroom example, I would explicitly state the hypothesis, the evidence, and the reference population before inserting any numbers. The denominator is often where intuition fails: the same evidence can occur even when the hypothesis is false.

**Discussion question:** how would you illustrate the role of the base rate without suggesting that the chosen prior is an objective fact?
MD,
        'comments' => [[3, 'A frequency table with a fixed population makes the denominator visible. I would then repeat the example with a different prior to show sensitivity.'], [4, 'Keep the labels consistent between the table and the equation. Switching terminology mid-example adds avoidable cognitive work.']],
    ],
    [
        'author' => 1,
        'title' => 'Where should a transaction end and a notification begin?',
        'tags' => ['Databases', 'Cloud Computing'],
        'body' => "A discussion reply may create a comment, award reputation, and notify another scholar. Sending the notification before commit can expose an event that later rolls back.\n\nI would keep the durable changes inside the transaction and dispatch the notification after commit. For stronger delivery guarantees, an outbox introduces a durable record of pending work. Where would you draw that boundary in a small application, and which failure cases would you test first?",
        'comments' => [[0, 'Start with a rollback test: deliberately fail after the comment write, then verify that neither the comment nor its reputation award remains. For delivery, after-commit dispatch avoids broadcasting rolled-back state; an outbox addresses a different gap, when the process fails after commit but before dispatch.'], [5, 'Also test retries. A notification consumer should tolerate duplicate delivery without presenting duplicate user-visible events.']],
    ],
    [
        'author' => 0,
        'title' => 'Designing a reputation ledger that can explain every point',
        'tags' => ['Databases', 'Web Security'],
        'body' => <<<'MD'
A reputation score is useful only when its history can explain it. If a vote is withdrawn, the balance should change while the original award remains visible.

## One balance, one verifiable history

An append-only ledger records the award and a separate compensating entry. This gives us a simple invariant to check after every committed operation:

```sql
SELECT user_id, SUM(delta) AS ledger_balance
FROM reputations
GROUP BY user_id;
```

The cached profile balance must equal that sum. A unique reference from a reversal to its original entry prevents the same award from being reversed twice.

## The concurrency question

Two requests may attempt the same change at once. I would serialize ledger writes for a scholar, keep the vote and its effects in one transaction, and let a database constraint guard against duplicate reversals.

**What would you test first:** a repeated request, simultaneous reversals, or a failure between the ledger insert and balance update? How would you demonstrate that the history remains understandable afterward?
MD,
        'comments' => [[1, 'I would start with the failure between writes. Assert that the transaction rolls back both changes, then repeat the operation successfully. For reversals, keep the original award, append its inverse, and assert that a second reversal does nothing.'], [2, 'The audit view should make a retraction easy to distinguish from a penalty. Both decrease the total, but they explain different events. Showing the source discussion alongside each entry would help.'], [5, 'The unique reversal reference is a useful final guard. I would also test a direct database update to confirm that append-only protection does not depend solely on the interface.']],
    ],
];
