import { describe, expect, it } from 'vitest';
import {
    assessmentScores,
    daysSince,
    dominantObstacle,
    hasCompletedCoaching,
    scoreFor,
} from './metrics.js';

const now = new Date('2026-11-12T00:00:00');

describe('hasCompletedCoaching', () => {
    it('is true only when the coaching session is completed', () => {
        expect(hasCompletedCoaching({ coachingSession: { status: 'completed' } })).toBe(true);
        expect(hasCompletedCoaching({ coachingSession: { status: 'scheduled' } })).toBe(false);
        expect(hasCompletedCoaching({})).toBe(false);
    });
});

describe('assessmentScores', () => {
    it('returns the current assessment scores', () => {
        const scores = [{ slug: 'modal', score: 80, level: 'high', obstacleCategoryId: 1 }];

        expect(assessmentScores({ currentAssessment: { scores } })).toEqual(scores);
    });

    it('returns an empty list when the record has no assessment', () => {
        expect(assessmentScores({})).toEqual([]);
    });
});

describe('scoreFor', () => {
    const record = {
        currentAssessment: {
            scores: [{ slug: 'modal', score: 80, level: 'high', obstacleCategoryId: 1 }],
        },
    };

    it('finds the score for a category slug', () => {
        expect(scoreFor(record, 'modal')).toEqual({
            slug: 'modal',
            score: 80,
            level: 'high',
            obstacleCategoryId: 1,
        });
    });

    it('returns null when the slug is absent', () => {
        expect(scoreFor(record, 'pemasaran')).toBeNull();
        expect(scoreFor({}, 'modal')).toBeNull();
    });
});

describe('dominantObstacle', () => {
    it('keeps the higher score', () => {
        const record = {
            currentAssessment: {
                scores: [
                    { slug: 'pemasaran', score: 40, level: 'moderate', obstacleCategoryId: 2 },
                    { slug: 'modal', score: 80, level: 'high', obstacleCategoryId: 1 },
                ],
            },
        };

        expect(dominantObstacle(record)).toEqual({
            slug: 'modal',
            score: 80,
            level: 'high',
            obstacleCategoryId: 1,
        });
    });

    it('uses the empty-list fallback when there are no scores', () => {
        expect(dominantObstacle({})).toEqual({
            slug: null,
            score: -1,
            level: 'low',
            obstacleCategoryId: null,
        });
    });
});

describe('daysSince', () => {
    it('counts whole days from a date string', () => {
        expect(daysSince('2026-11-10', now)).toBe(2);
    });

    it('returns zero for an empty date', () => {
        expect(daysSince('', now)).toBe(0);
        expect(daysSince(null, now)).toBe(0);
    });

    it('returns zero for an invalid date', () => {
        expect(daysSince('bukan-tanggal', now)).toBe(0);
    });

    it('returns zero when the date is still in the future', () => {
        expect(daysSince('2026-11-20', now)).toBe(0);
    });
});
