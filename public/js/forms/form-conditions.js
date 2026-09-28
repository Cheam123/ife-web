/**
 * Shared condition evaluator — behavioural twin of
 * app/Services/FormConditionEvaluator.php + FormSchemaService::resolveVisibility().
 *
 * Dependency-free by design so the React Native app can copy it verbatim.
 *
 * Condition schema: { logic:"or", groups:[{ logic:"and", conditions:[
 *   { field:"el_x", operator:"equals|not_equals|includes|not_includes|gt|lt|is_empty|is_not_empty", value:"..." }
 * ]}]}
 * Groups are OR'd; conditions inside a group are AND'd; null schema = always true.
 */
(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.FormConditions = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    function isEmptyValue(value) {
        if (value === null || value === undefined || value === '') return true;
        if (Array.isArray(value)) {
            return value.filter(function (v) { return v !== null && v !== undefined && v !== ''; }).length === 0;
        }
        return false;
    }

    function scalar(value) {
        return Array.isArray(value) ? value.join(',') : value;
    }

    function compare(actual, expected) {
        if (isEmptyValue(actual)) return 0; // empty never satisfies gt/lt
        var a = scalar(actual);
        var numA = parseFloat(a), numB = parseFloat(expected);
        if (!isNaN(numA) && !isNaN(numB) && String(numA) === String(a).trim() && String(numB) === String(expected).trim()) {
            return numA > numB ? 1 : (numA < numB ? -1 : 0);
        }
        a = String(a); var b = String(expected);
        return a > b ? 1 : (a < b ? -1 : 0);
    }

    function evaluateCondition(condition, answersById) {
        var operator = condition.operator;
        var expected = condition.value;
        var actual = answersById[condition.field];
        if (actual === undefined) actual = null;

        switch (operator) {
            case 'equals':
                return !isEmptyValue(actual) && String(scalar(actual)) === String(expected);
            case 'not_equals':
                return isEmptyValue(actual) || String(scalar(actual)) !== String(expected);
            case 'includes':
                return Array.isArray(actual) && actual.map(String).indexOf(String(expected)) !== -1;
            case 'not_includes':
                return !Array.isArray(actual) || actual.map(String).indexOf(String(expected)) === -1;
            case 'gt':
                return compare(actual, expected) === 1;
            case 'lt':
                return compare(actual, expected) === -1;
            case 'is_empty':
                return isEmptyValue(actual);
            case 'is_not_empty':
                return !isEmptyValue(actual);
            default:
                return false; // unknown operator: fail closed
        }
    }

    function evaluateGroup(group, answersById) {
        var conditions = (group && group.conditions) || [];
        if (conditions.length === 0) return false;
        for (var i = 0; i < conditions.length; i++) {
            if (!evaluateCondition(conditions[i], answersById)) return false; // AND
        }
        return true;
    }

    function evaluate(schema, answersById) {
        if (!schema || !schema.groups || schema.groups.length === 0) return true;
        for (var i = 0; i < schema.groups.length; i++) {
            if (evaluateGroup(schema.groups[i], answersById)) return true; // OR
        }
        return false;
    }

    /**
     * schema = { groups:[{id,label,order,visible_when}], elements:[{id,group_id,visible_when,...}] }
     * Returns { elementId: bool }, cascading group visibility and treating
     * answers of hidden fields as empty, iterated to a fixed point.
     */
    function resolveVisibility(schema, answersById) {
        var elements = (schema && schema.elements) || [];
        var groups = (schema && schema.groups) || [];

        var visible = {};
        elements.forEach(function (el) { visible[el.id] = true; });

        for (var pass = 0; pass < 10; pass++) {
            var effective = {};
            Object.keys(answersById).forEach(function (id) {
                effective[id] = (visible[id] === undefined || visible[id]) ? answersById[id] : null;
            });

            var groupVisible = {};
            groups.forEach(function (g) {
                groupVisible[g.id] = evaluate(g.visible_when || null, effective);
            });

            var changed = false;
            elements.forEach(function (el) {
                var own = evaluate(el.visible_when || null, effective);
                var grp = el.group_id ? (groupVisible[el.group_id] !== undefined ? groupVisible[el.group_id] : true) : true;
                var next = own && grp;
                if (next !== visible[el.id]) changed = true;
                visible[el.id] = next;
            });

            if (!changed) break;
        }

        return visible;
    }

    return {
        evaluate: evaluate,
        isEmptyValue: isEmptyValue,
        resolveVisibility: resolveVisibility
    };
}));
